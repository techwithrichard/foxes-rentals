<?php

namespace App\Models;

use App\Enums\PaymentProofStatusEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Payment extends Model
{
    use HasUuids;
    use LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'paid_at' => 'date',
        'landlord_commission' => 'float',
    ];

    //append landlord_commission
    protected $appends = ['landlord_commission', 'company_income_amount'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['amount', 'payment_method', 'reference_number', 'payment_method', 'paid_at', 'tenant.name'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by', 'id')
            ->withDefault(['name' => 'System']);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'id');
    }

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id', 'id');
    }


    //returns the landlord net after deducting the commission
    public function getLandlordCommissionAttribute(): float
    {


        return ((100 - $this->commission) * $this->amount) / 100;
    }


    //returns the agency income from the commission percentage
    public function getCompanyIncomeAmountAttribute(): float
    {
        return ($this->commission * $this->amount) / 100;
    }


}
