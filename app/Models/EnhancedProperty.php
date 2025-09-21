<?php

namespace App\Models;

use App\Enums\PropertyStatusEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EnhancedProperty extends Model
{
    use HasUuids, HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'description',
        'property_type_id',
        'landlord_id',
        'status',
        'is_active',
        'is_featured',
        'is_published',
        'propertyable_type',
        'propertyable_id',
        'latitude',
        'longitude',
        'year_built',
        'last_renovated',
        'property_size',
        'lot_size',
        'bedrooms',
        'bathrooms',
        'parking_spaces',
        'features',
        'images',
        'virtual_tour',
        'marketing_description',
        'keywords',
        'seo_title',
        'seo_description',
        'views_count',
        'inquiries_count',
        'applications_count',
        'published_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'features' => 'array',
        'images' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'property_size' => 'decimal:2',
        'lot_size' => 'decimal:2',
        'year_built' => 'integer',
        'last_renovated' => 'integer',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'parking_spaces' => 'integer',
        'views_count' => 'integer',
        'inquiries_count' => 'integer',
        'applications_count' => 'integer',
    ];

    /**
     * Get the polymorphic relationship to rental, sale, or lease properties
     */
    public function propertyable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the property type
     */
    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    /**
     * Get the landlord
     */
    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    /**
     * Get the address
     */
    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    /**
     * Get all leases for this property
     */
    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class, 'property_id');
    }

    /**
     * Get active leases
     */
    public function activeLeases(): HasMany
    {
        return $this->hasMany(Lease::class, 'property_id')
            ->where('status', 'active');
    }

    /**
     * Get maintenance requests
     */
    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'property_id');
    }

    /**
     * Get property inquiries
     */
    public function inquiries(): HasMany
    {
        return $this->hasMany(PropertyInquiry::class, 'property_id');
    }

    /**
     * Get property applications
     */
    public function applications(): HasMany
    {
        return $this->hasMany(PropertyApplication::class, 'property_id');
    }

    /**
     * Get property units (for multi-unit properties)
     */
    public function units(): HasMany
    {
        return $this->hasMany(PropertyUnit::class, 'property_id');
    }

    /**
     * Get available units
     */
    public function availableUnits(): HasMany
    {
        return $this->hasMany(PropertyUnit::class, 'property_id')
            ->where('is_available', true);
    }

    /**
     * Scope for active properties
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for published properties
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for featured properties
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for properties by status
     */
    public function scopeByStatus($query, PropertyStatusEnum $status)
    {
        return $query->where('status', $status->value);
    }

    /**
     * Scope for available properties
     */
    public function scopeAvailable($query)
    {
        return $query->whereIn('status', [
            PropertyStatusEnum::AVAILABLE->value,
            PropertyStatusEnum::VACANT->value,
        ]);
    }

    /**
     * Scope for occupied properties
     */
    public function scopeOccupied($query)
    {
        return $query->where('status', PropertyStatusEnum::OCCUPIED->value);
    }

    /**
     * Scope for properties requiring maintenance
     */
    public function scopeRequiringMaintenance($query)
    {
        return $query->whereIn('status', [
            PropertyStatusEnum::MAINTENANCE->value,
            PropertyStatusEnum::RENOVATION->value,
        ]);
    }

    /**
     * Scope for properties by type
     */
    public function scopeByType($query, $propertyTypeId)
    {
        return $query->where('property_type_id', $propertyTypeId);
    }

    /**
     * Scope for properties by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->whereHas('propertyType', function($q) use ($category) {
            $q->where('category', $category);
        });
    }

    /**
     * Scope for properties by landlord
     */
    public function scopeByLandlord($query, $landlordId)
    {
        return $query->where('landlord_id', $landlordId);
    }

    /**
     * Scope for properties in location
     */
    public function scopeInLocation($query, $city, $state = null)
    {
        return $query->whereHas('address', function($q) use ($city, $state) {
            $q->where('city', 'like', "%{$city}%");
            if ($state) {
                $q->where('state', 'like', "%{$state}%");
            }
        });
    }

    /**
     * Scope for properties with price range
     */
    public function scopeWithPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereHas('propertyable', function($q) use ($minPrice, $maxPrice) {
            $q->where(function($subQuery) use ($minPrice, $maxPrice) {
                // For rental properties
                $subQuery->where(function($rentalQuery) use ($minPrice, $maxPrice) {
                    $rentalQuery->where('propertyable_type', RentalProperty::class)
                        ->whereBetween('rent_amount', [$minPrice, $maxPrice]);
                })
                // For sale properties
                ->orWhere(function($saleQuery) use ($minPrice, $maxPrice) {
                    $saleQuery->where('propertyable_type', SaleProperty::class)
                        ->whereBetween('sale_price', [$minPrice, $maxPrice]);
                })
                // For lease properties
                ->orWhere(function($leaseQuery) use ($minPrice, $maxPrice) {
                    $leaseQuery->where('propertyable_type', LeaseProperty::class)
                        ->whereBetween('lease_amount', [$minPrice, $maxPrice]);
                });
            });
        });
    }

    /**
     * Get the current status enum
     */
    public function getStatusEnum(): PropertyStatusEnum
    {
        return PropertyStatusEnum::from($this->status);
    }

    /**
     * Check if property is available for rent/sale/lease
     */
    public function isAvailable(): bool
    {
        return $this->getStatusEnum()->allowsOccupancy();
    }

    /**
     * Check if property requires maintenance
     */
    public function requiresMaintenance(): bool
    {
        return $this->getStatusEnum()->requiresMaintenance();
    }

    /**
     * Get occupancy rate
     */
    public function getOccupancyRate(): float
    {
        $totalUnits = $this->units()->count();
        if ($totalUnits === 0) {
            return $this->activeLeases()->count() > 0 ? 100 : 0;
        }

        $occupiedUnits = $this->activeLeases()->count();
        return ($occupiedUnits / $totalUnits) * 100;
    }

    /**
     * Get total revenue for a period
     */
    public function getTotalRevenue($period = 12): float
    {
        $startDate = now()->subMonths($period);
        
        return $this->leases()
            ->where('start_date', '>=', $startDate)
            ->sum('rent');
    }

    /**
     * Get total maintenance costs for a period
     */
    public function getTotalMaintenanceCosts($period = 12): float
    {
        $startDate = now()->subMonths($period);
        
        return $this->maintenanceRequests()
            ->where('created_at', '>=', $startDate)
            ->sum('cost');
    }

    /**
     * Increment view count
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Increment inquiry count
     */
    public function incrementInquiries(): void
    {
        $this->increment('inquiries_count');
    }

    /**
     * Increment application count
     */
    public function incrementApplications(): void
    {
        $this->increment('applications_count');
    }

    /**
     * Get activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name', 'description', 'status', 'is_active', 'is_featured',
                'property_type_id', 'landlord_id', 'property_size', 'bedrooms', 'bathrooms'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
