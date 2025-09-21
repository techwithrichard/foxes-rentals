<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyType extends Model
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'category', // 'residential', 'office', 'retail', 'industrial', 'hospitality', 'healthcare', 'mixed-use', 'land'
        'is_active',
        'sort_order',
        'icon',
        'color',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Note: The old Property model doesn't have property_type_id column
    // Only the new property models (RentalProperty, SaleProperty, LeaseProperty) have this relationship

    public function rentalProperties(): HasMany
    {
        return $this->hasMany(RentalProperty::class);
    }

    public function saleProperties(): HasMany
    {
        return $this->hasMany(SaleProperty::class);
    }

    public function leaseProperties(): HasMany
    {
        return $this->hasMany(LeaseProperty::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}