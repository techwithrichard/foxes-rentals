<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyApplication extends Model
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id', 'property_type', 'applicant_id', 'status',
        'application_date', 'desired_move_in_date', 'lease_duration',
        'monthly_income', 'employment_status', 'employer_name',
        'employment_duration', 'references', 'emergency_contact',
        'pets', 'smoking', 'special_requirements', 'notes',
        'application_fee', 'application_fee_paid', 'documents',
        'background_check_status', 'credit_check_status',
        'approval_date', 'rejection_reason', 'approved_by'
    ];

    protected $casts = [
        'application_date' => 'datetime', 'desired_move_in_date' => 'date',
        'approval_date' => 'datetime', 'application_fee_paid' => 'boolean',
        'pets' => 'boolean', 'smoking' => 'boolean',
        'references' => 'array', 'emergency_contact' => 'array',
        'special_requirements' => 'array', 'documents' => 'array',
        'monthly_income' => 'decimal:2', 'application_fee' => 'decimal:2',
        'lease_duration' => 'integer', 'employment_duration' => 'integer'
    ];

    public function property(): MorphTo
    {
        return $this->morphTo();
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
