<?php

namespace App\Models;

use App\Enums\InvoicableTypeEnum;
use App\Enums\PaymentStatusEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'invoicable_type' => InvoicableTypeEnum::class,
        'status' => PaymentStatusEnum::class,
        'bills' => 'array',
    ];

    // add auto incrementing invoice_id when creating new invoice

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->invoice_id = Invoice::max('invoice_id') + 1;
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id', 'id')
            ->withDefault(['name' => 'Archived']);

    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    //has many verified payments
    public function verified_payments(): HasMany
    {
        return $this->hasMany(Payment::class)->where('status', PaymentStatusEnum::PAID);
    }

    //cancelled payments
    public function cancelled_payments(): HasMany
    {
        return $this->hasMany(Payment::class)->where('status', PaymentStatusEnum::CANCELLED);
    }

    //pending payments
    public function pending_payments(): HasMany
    {
        return $this->hasMany(Payment::class)->where('status', PaymentStatusEnum::PENDING);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('amount', '>', $this->payments()->sum('amount'));
    }

    public function scopeUnpaidStatus($query)
    {
        //where status is either pending, overdue or partially paid
        return $query->whereIn('status', [PaymentStatusEnum::PENDING, PaymentStatusEnum::OVERDUE, PaymentStatusEnum::PARTIALLY_PAID]);
    }


    public function getCommissionAttribute()
    {
        return $this->house_id ? $this->house->commission : $this->property->commission;
    }

    public function getLandlordIdAttribute()
    {
        return $this->house_id ? $this->house->landlord_id : $this->property->landlord_id;
    }

    public function getBalanceDueAttribute()
    {
        return $this->amount + $this->bills_amount - $this->paid_amount;
    }

    //pay invoice that takes in a float,and adds it to paid_amount field in invoice model
    public function pay(float $amount)
    {
        $this->paid_amount += $amount;
        $this->save();
        $this->updateStatus(); // Update status after payment
    }

    /**
     * Add bills to invoice and update status
     */
    public function addBills(array $bills)
    {
        $existingBills = $this->bills ?? [];
        $totalBillsAmount = 0;
        
        foreach ($bills as $bill) {
            $existingBills[] = $bill;
            $totalBillsAmount += $bill['amount'];
        }
        
        $this->bills = $existingBills;
        $this->bills_amount += $totalBillsAmount;
        $this->save();
        $this->updateStatus(); // Update status after adding bills
        
        return $this;
    }

    /**
     * Add a single bill to invoice
     */
    public function addBill(string $name, float $amount)
    {
        return $this->addBills([['name' => $name, 'amount' => $amount]]);
    }

    //update invoice status after reversing payment
    public function updateStatus(): void
    {

        //refresh the invoce before updating status to avoid stale data

        $this->refresh();
        if ($this->paid_amount == 0) {
            $this->status = PaymentStatusEnum::PENDING->value;
        } elseif ($this->paid_amount < ($this->amount + $this->bills_amount)) {
            $this->status = PaymentStatusEnum::PARTIALLY_PAID->value;
        } elseif ($this->paid_amount == ($this->amount + $this->bills_amount)) {
            $this->status = PaymentStatusEnum::PAID->value;
        } elseif ($this->paid_amount > ($this->amount + $this->bills_amount)) {
            $this->status = PaymentStatusEnum::OVER_PAID->value;
        }
        $this->save();
    }

    /**
     * Get account number for paybill payments
     * Uses lease_reference for unique, secure account numbers
     */
    public function getAccountNumber(): string
    {
        return $this->lease_reference ?? 'INV' . str_pad($this->invoice_id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Find invoice by account number (lease reference or fallback to invoice_id)
     */
    public static function findByAccountNumber(string $accountNumber): ?self
    {
        // First try to find by lease_reference (preferred method)
        if ($accountNumber && !str_starts_with($accountNumber, 'INV')) {
            $invoice = self::where('lease_reference', $accountNumber)->first();
            if ($invoice) {
                return $invoice;
            }
        }
        
        // Fallback: try INV format
        if (str_starts_with($accountNumber, 'INV')) {
            $invoiceId = (int) substr($accountNumber, 3);
            return self::where('invoice_id', $invoiceId)->first();
        }
        
        // Fallback: try to find by invoice_id directly
        if (is_numeric($accountNumber)) {
            return self::where('invoice_id', (int) $accountNumber)->first();
        }
        
        return null;
    }

    /**
     * @deprecated Use getAccountNumber() instead
     */
    public function getShortAccountNumber(): string
    {
        return $this->getAccountNumber();
    }

    /**
     * @deprecated Use findByAccountNumber() instead
     */
    public static function findByShortAccountNumber(string $accountNumber): ?self
    {
        return self::findByAccountNumber($accountNumber);
    }


}
