<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PropertyAmenity extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'category',
        'icon',
        'is_chargeable',
        'default_cost',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_chargeable' => 'boolean',
        'is_active' => 'boolean',
        'default_cost' => 'decimal:2',
        'sort_order' => 'integer'
    ];

    /**
     * Get rental properties that have this amenity
     */
    public function rentalProperties(): BelongsToMany
    {
        return $this->belongsToMany(RentalProperty::class, 'rental_property_amenities')
            ->withPivot('cost', 'is_included')
            ->withTimestamps();
    }

    /**
     * Get all properties (rental + sale) that have this amenity
     * This is a virtual relationship for counting purposes
     */
    public function properties()
    {
        // This method is used for withCount() - it returns a query builder
        // that can count both rental and sale properties
        return $this->rentalProperties();
    }

    /**
     * Get sale properties that have this amenity
     */
    public function saleProperties(): BelongsToMany
    {
        return $this->belongsToMany(SaleProperty::class, 'sale_property_amenities')
            ->withPivot('cost', 'is_included')
            ->withTimestamps();
    }

    /**
     * Scope to get active amenities
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get amenities by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to get chargeable amenities
     */
    public function scopeChargeable($query)
    {
        return $query->where('is_chargeable', true);
    }

    /**
     * Scope to get included amenities
     */
    public function scopeIncluded($query)
    {
        return $query->where('is_chargeable', false);
    }

    /**
     * Scope to order by category and sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('category')->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the display name with category
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->category ? "{$this->category} - {$this->name}" : $this->name;
    }

    /**
     * Get the category display name
     */
    public function getCategoryDisplayNameAttribute(): string
    {
        $categories = [
            'security' => 'Security',
            'utilities' => 'Utilities',
            'recreation' => 'Recreation',
            'transportation' => 'Transportation',
            'maintenance' => 'Maintenance',
            'technology' => 'Technology',
            'kitchen' => 'Kitchen',
            'bathroom' => 'Bathroom',
            'bedroom' => 'Bedroom',
            'living' => 'Living Area',
            'outdoor' => 'Outdoor',
            'parking' => 'Parking',
            'storage' => 'Storage',
            'business' => 'Business',
            'health' => 'Health & Fitness'
        ];

        return $categories[$this->category] ?? ucfirst(str_replace('_', ' ', $this->category ?? 'General'));
    }

    /**
     * Get the formatted cost
     */
    public function getFormattedCostAttribute(): string
    {
        if (!$this->is_chargeable || !$this->default_cost) {
            return 'Included';
        }
        return 'Kshs. ' . number_format($this->default_cost, 2);
    }

    /**
     * Get icon HTML
     */
    public function getIconHtmlAttribute(): string
    {
        if (!$this->icon) {
            return '<em class="icon ni ni-star"></em>';
        }

        // Check if it's a custom icon or a class
        if (str_starts_with($this->icon, 'ni-')) {
            return "<em class=\"icon ni {$this->icon}\"></em>";
        }

        return "<i class=\"{$this->icon}\"></i>";
    }

    /**
     * Get usage statistics
     */
    public function getUsageStatsAttribute(): array
    {
        return [
            'total_properties' => $this->properties()->count() + $this->saleProperties()->count(),
            'rental_properties' => $this->properties()->count(),
            'sale_properties' => $this->saleProperties()->count(),
            'average_cost' => $this->properties()->avg('property_amenities.cost') ?? 0,
            'last_used' => $this->properties()->latest()->first()?->created_at
        ];
    }

    /**
     * Check if amenity is used by any properties
     */
    public function isInUse(): bool
    {
        return $this->properties()->exists() || $this->saleProperties()->exists();
    }

    /**
     * Get suggested categories
     */
    public static function getSuggestedCategories(): array
    {
        return [
            'security' => 'Security',
            'utilities' => 'Utilities',
            'recreation' => 'Recreation',
            'transportation' => 'Transportation',
            'maintenance' => 'Maintenance',
            'technology' => 'Technology',
            'kitchen' => 'Kitchen',
            'bathroom' => 'Bathroom',
            'bedroom' => 'Bedroom',
            'living' => 'Living Area',
            'outdoor' => 'Outdoor',
            'parking' => 'Parking',
            'storage' => 'Storage',
            'business' => 'Business',
            'health' => 'Health & Fitness'
        ];
    }

    /**
     * Get suggested icons
     */
    public static function getSuggestedIcons(): array
    {
        return [
            'security' => 'ni-shield-star',
            'utilities' => 'ni-power',
            'recreation' => 'ni-swimming',
            'transportation' => 'ni-bus',
            'maintenance' => 'ni-tools',
            'technology' => 'ni-wifi',
            'kitchen' => 'ni-home',
            'bathroom' => 'ni-droplet',
            'bedroom' => 'ni-bed',
            'living' => 'ni-tv',
            'outdoor' => 'ni-tree',
            'parking' => 'ni-car',
            'storage' => 'ni-box',
            'business' => 'ni-building',
            'health' => 'ni-heart'
        ];
    }

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($amenity) {
            if (is_null($amenity->sort_order)) {
                $amenity->sort_order = static::where('category', $amenity->category)->max('sort_order') + 1;
            }
        });
    }
}
