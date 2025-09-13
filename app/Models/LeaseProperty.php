<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LeaseProperty extends Model
{
    use HasUuids, HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name', 'description', 'property_type_id', 'landlord_id', 'lease_amount',
        'commission_rate', 'status', 'is_available', 'lease_duration_months',
        'minimum_lease_period', 'maximum_lease_period', 'renewal_terms',
        'deposit_amount', 'features', 'images', 'latitude', 'longitude',
        'year_built', 'property_size', 'bedrooms', 'bathrooms',
        'is_featured', 'is_published', 'published_at', 'views_count',
        'inquiries_count', 'applications_count', 'lease_terms',
        'special_conditions', 'marketing_description'
    ];

    protected $casts = [
        'is_available' => 'boolean', 'is_featured' => 'boolean',
        'is_published' => 'boolean', 'published_at' => 'datetime',
        'lease_amount' => 'decimal:2', 'commission_rate' => 'decimal:2',
        'deposit_amount' => 'decimal:2', 'features' => 'array',
        'images' => 'array', 'lease_terms' => 'array',
        'special_conditions' => 'array', 'views_count' => 'integer',
        'inquiries_count' => 'integer', 'applications_count' => 'integer',
        'lease_duration_months' => 'integer', 'minimum_lease_period' => 'integer',
        'maximum_lease_period' => 'integer', 'year_built' => 'integer',
        'property_size' => 'decimal:2', 'bedrooms' => 'integer',
        'bathrooms' => 'integer', 'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'lease_amount', 'status', 'is_available'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function leaseAgreements(): HasMany
    {
        return $this->hasMany(LeaseAgreement::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(PropertyInquiry::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(PropertyApplication::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function getFormattedLeaseAmountAttribute()
    {
        return 'Kshs. ' . number_format($this->lease_amount, 2);
    }
}
