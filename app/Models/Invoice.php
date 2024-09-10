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


}
