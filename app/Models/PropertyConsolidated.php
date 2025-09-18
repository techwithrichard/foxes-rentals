<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertyConsolidated extends Model
{
    use HasUuids, HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'properties_consolidated';

    protected $fillable = [
        'name', 'description', 'property_type_id', 'landlord_id',
        'property_subtype', 'base_amount', 'deposit_amount', 'commission_rate',
        'status', 'is_available', 'is_vacant', 'is_multi_unit', 'total_units', 'available_units',
        'electricity_id', 'water_id', 'furnished', 'pet_friendly', 'smoking_allowed',
        'parking_spaces', 'balcony', 'garden', 'swimming_pool', 'gym', 'security',
        'elevator', 'air_conditioning', 'heating', 'internet', 'cable_tv', 'laundry',
        'dishwasher', 'microwave', 'refrigerator', 'stove', 'oven', 'features', 'images',
        'floor_plan', 'virtual_tour', 'latitude', 'longitude', 'year_built', 'last_renovated',
        'property_size', 'lot_size', 'bedrooms', 'bathrooms', 'living_rooms', 'kitchens',
        'dining_rooms', 'storage_rooms', 'garage_spaces', 'outdoor_spaces',
        'utilities_included', 'maintenance_responsibility', 'lease_terms',
        'minimum_lease_period', 'maximum_lease_period', 'notice_period',
        'late_fee_percentage', 'late_fee_fixed', 'returned_check_fee', 'early_termination_fee',
        'renewal_terms', 'special_conditions', 'marketing_description', 'keywords',
        'seo_title', 'seo_description', 'is_featured', 'is_published', 'published_at',
        'views_count', 'inquiries_count', 'applications_count'
    ];

    protected $casts = [
        'features' => 'array',
        'images' => 'array',
        'utilities_included' => 'array',
        'lease_terms' => 'array',
        'renewal_terms' => 'array',
        'special_conditions' => 'array',
        'base_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'property_size' => 'decimal:2',
        'lot_size' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'late_fee_percentage' => 'decimal:2',
        'late_fee_fixed' => 'decimal:2',
        'returned_check_fee' => 'decimal:2',
        'early_termination_fee' => 'decimal:2',
        'is_available' => 'boolean',
        'is_vacant' => 'boolean',
        'is_multi_unit' => 'boolean',
        'furnished' => 'boolean',
        'pet_friendly' => 'boolean',
        'smoking_allowed' => 'boolean',
        'balcony' => 'boolean',
        'garden' => 'boolean',
        'swimming_pool' => 'boolean',
        'gym' => 'boolean',
        'security' => 'boolean',
        'elevator' => 'boolean',
        'air_conditioning' => 'boolean',
        'heating' => 'boolean',
        'internet' => 'boolean',
        'cable_tv' => 'boolean',
        'laundry' => 'boolean',
        'dishwasher' => 'boolean',
        'microwave' => 'boolean',
        'refrigerator' => 'boolean',
        'stove' => 'boolean',
        'oven' => 'boolean',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'base_amount', 'status', 'is_vacant', 'commission_rate'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relationships
    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PropertyDetail::class);
    }

    public function rentalDetails(): HasOne
    {
        return $this->hasOne(PropertyDetail::class)->where('detail_type', 'rental');
    }

    public function saleDetails(): HasOne
    {
        return $this->hasOne(PropertyDetail::class)->where('detail_type', 'sale');
    }

    public function leaseDetails(): HasOne
    {
        return $this->hasOne(PropertyDetail::class)->where('detail_type', 'lease');
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }

    public function activeLeases(): HasMany
    {
        return $this->hasMany(Lease::class)->where('status', 'active');
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(PropertyInquiry::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(PropertyApplication::class);
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    // Scopes
    public function scopeRental($query)
    {
        return $query->where('property_subtype', 'rental');
    }

    public function scopeSale($query)
    {
        return $query->where('property_subtype', 'sale');
    }

    public function scopeLease($query)
    {
        return $query->where('property_subtype', 'lease');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeVacant($query)
    {
        return $query->where('is_vacant', true);
    }

    public function scopeOccupied($query)
    {
        return $query->where('is_vacant', false);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    // Accessors
    public function getRentAmountAttribute()
    {
        return $this->property_subtype === 'rental' ? $this->base_amount : null;
    }

    public function getSalePriceAttribute()
    {
        return $this->property_subtype === 'sale' ? $this->base_amount : null;
    }

    public function getLeaseAmountAttribute()
    {
        return $this->property_subtype === 'lease' ? $this->base_amount : null;
    }

    public function getFormattedAmountAttribute()
    {
        return 'KSh ' . number_format($this->base_amount, 2);
    }

    // Helper methods
    public function isRental(): bool
    {
        return $this->property_subtype === 'rental';
    }

    public function isSale(): bool
    {
        return $this->property_subtype === 'sale';
    }

    public function isLease(): bool
    {
        return $this->property_subtype === 'lease';
    }

    public function getDetailData(string $type): ?array
    {
        $detail = $this->details()->where('detail_type', $type)->first();
        return $detail ? $detail->detail_data : null;
    }

    public function setDetailData(string $type, array $data): void
    {
        $this->details()->updateOrCreate(
            ['detail_type' => $type],
            ['detail_data' => $data]
        );
    }
}
