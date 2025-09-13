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

class RentalProperty extends Model
{
    use HasUuids, HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'description',
        'property_type_id',
        'landlord_id',
        'rent_amount',
        'deposit_amount',
        'commission_rate',
        'electricity_id',
        'water_id',
        'status',
        'is_vacant',
        'is_multi_unit',
        'total_units',
        'available_units',
        'furnished',
        'pet_friendly',
        'smoking_allowed',
        'parking_spaces',
        'balcony',
        'garden',
        'swimming_pool',
        'gym',
        'security',
        'elevator',
        'air_conditioning',
        'heating',
        'internet',
        'cable_tv',
        'laundry',
        'dishwasher',
        'microwave',
        'refrigerator',
        'stove',
        'oven',
        'features',
        'images',
        'floor_plan',
        'virtual_tour',
        'latitude',
        'longitude',
        'year_built',
        'last_renovated',
        'property_size',
        'lot_size',
        'bedrooms',
        'bathrooms',
        'living_rooms',
        'kitchens',
        'dining_rooms',
        'storage_rooms',
        'garage_spaces',
        'outdoor_spaces',
        'utilities_included',
        'maintenance_responsibility',
        'lease_terms',
        'minimum_lease_period',
        'maximum_lease_period',
        'notice_period',
        'late_fee_percentage',
        'late_fee_fixed',
        'returned_check_fee',
        'early_termination_fee',
        'renewal_terms',
        'special_conditions',
        'marketing_description',
        'keywords',
        'seo_title',
        'seo_description',
        'is_featured',
        'is_published',
        'published_at',
        'views_count',
        'inquiries_count',
        'applications_count',
    ];

    protected $casts = [
        'is_vacant' => 'boolean',
        'is_multi_unit' => 'boolean',
        'furnished' => 'boolean',
        'pet_friendly' => 'boolean',
        'smoking_allowed' => 'boolean',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'rent_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'features' => 'array',
        'images' => 'array',
        'utilities_included' => 'array',
        'lease_terms' => 'array',
        'special_conditions' => 'array',
        'views_count' => 'integer',
        'inquiries_count' => 'integer',
        'applications_count' => 'integer',
        'total_units' => 'integer',
        'available_units' => 'integer',
        'parking_spaces' => 'integer',
        'year_built' => 'integer',
        'last_renovated' => 'integer',
        'property_size' => 'decimal:2',
        'lot_size' => 'decimal:2',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'living_rooms' => 'integer',
        'kitchens' => 'integer',
        'dining_rooms' => 'integer',
        'storage_rooms' => 'integer',
        'garage_spaces' => 'integer',
        'outdoor_spaces' => 'integer',
        'minimum_lease_period' => 'integer',
        'maximum_lease_period' => 'integer',
        'notice_period' => 'integer',
        'late_fee_percentage' => 'decimal:2',
        'late_fee_fixed' => 'decimal:2',
        'returned_check_fee' => 'decimal:2',
        'early_termination_fee' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'rent_amount', 'status', 'is_vacant', 'commission_rate'])
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

    public function units(): HasMany
    {
        return $this->hasMany(RentalUnit::class);
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

    public function scopeByRentRange($query, $min, $max)
    {
        return $query->whereBetween('rent_amount', [$min, $max]);
    }

    public function scopeByBedrooms($query, $bedrooms)
    {
        return $query->where('bedrooms', $bedrooms);
    }

    public function scopeByBathrooms($query, $bathrooms)
    {
        return $query->where('bathrooms', $bathrooms);
    }

    public function scopeByPropertyType($query, $propertyTypeId)
    {
        return $query->where('property_type_id', $propertyTypeId);
    }

    public function scopeByLocation($query, $city, $state = null)
    {
        return $query->whereHas('address', function ($q) use ($city, $state) {
            $q->where('city', 'like', "%{$city}%");
            if ($state) {
                $q->where('state', 'like', "%{$state}%");
            }
        });
    }

    public function scopeWithFeatures($query, $features)
    {
        return $query->whereJsonContains('features', $features);
    }

    public function getFormattedRentAttribute()
    {
        return 'Kshs. ' . number_format($this->rent_amount, 2);
    }

    public function getFormattedDepositAttribute()
    {
        return 'Kshs. ' . number_format($this->deposit_amount, 2);
    }

    public function getOccupancyRateAttribute()
    {
        if ($this->total_units == 0) {
            return 0;
        }
        return round((($this->total_units - $this->available_units) / $this->total_units) * 100, 2);
    }

    public function getIsFullyOccupiedAttribute()
    {
        return $this->available_units <= 0;
    }

    public function getIsPartiallyOccupiedAttribute()
    {
        return $this->available_units > 0 && $this->available_units < $this->total_units;
    }
}
