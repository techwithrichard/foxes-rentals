<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaseAgreement extends Model
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $fillable = [
        'lease_property_id', 'tenant_id', 'landlord_id', 'lease_number',
        'start_date', 'end_date', 'monthly_amount', 'deposit_amount',
        'status', 'lease_terms', 'special_conditions', 'renewal_terms',
        'termination_terms', 'maintenance_responsibilities', 'utilities',
        'pet_policy', 'smoking_policy', 'guest_policy', 'subletting_policy',
        'late_fee_percentage', 'late_fee_fixed', 'returned_check_fee',
        'early_termination_fee', 'security_deposit_terms', 'notes',
        'signed_date', 'executed_by_tenant', 'executed_by_landlord',
        'witness_name', 'witness_signature', 'document_path'
    ];

    protected $casts = [
        'start_date' => 'date', 'end_date' => 'date', 'signed_date' => 'datetime',
        'executed_by_tenant' => 'boolean', 'executed_by_landlord' => 'boolean',
        'monthly_amount' => 'decimal:2', 'deposit_amount' => 'decimal:2',
        'late_fee_percentage' => 'decimal:2', 'late_fee_fixed' => 'decimal:2',
        'returned_check_fee' => 'decimal:2', 'early_termination_fee' => 'decimal:2',
        'lease_terms' => 'array', 'special_conditions' => 'array',
        'renewal_terms' => 'array', 'termination_terms' => 'array',
        'maintenance_responsibilities' => 'array', 'utilities' => 'array',
        'pet_policy' => 'array', 'smoking_policy' => 'array',
        'guest_policy' => 'array', 'subletting_policy' => 'array',
        'security_deposit_terms' => 'array'
    ];

    public function leaseProperty(): BelongsTo
    {
        return $this->belongsTo(LeaseProperty::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeTerminated($query)
    {
        return $query->where('status', 'terminated');
    }

    public function getFormattedMonthlyAmountAttribute()
    {
        return 'Kshs. ' . number_format($this->monthly_amount, 2);
    }

    public function getIsExpiringSoonAttribute()
    {
        return $this->end_date->diffInDays(now()) <= 30;
    }
}
