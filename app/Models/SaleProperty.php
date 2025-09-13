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

class SaleProperty extends Model
{
    use HasUuids, HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name', 'description', 'property_type_id', 'landlord_id', 'sale_price',
        'commission_rate', 'status', 'is_available', 'furnished', 'pet_friendly',
        'parking_spaces', 'features', 'images', 'latitude', 'longitude',
        'year_built', 'property_size', 'lot_size', 'bedrooms', 'bathrooms',
        'garage_spaces', 'is_featured', 'is_published', 'published_at',
        'views_count', 'inquiries_count', 'offers_count', 'sale_terms',
        'special_conditions', 'marketing_description', 'keywords'
    ];

    protected $casts = [
        'is_available' => 'boolean', 'furnished' => 'boolean', 'pet_friendly' => 'boolean',
        'is_featured' => 'boolean', 'is_published' => 'boolean', 'published_at' => 'datetime',
        'sale_price' => 'decimal:2', 'commission_rate' => 'decimal:2',
        'features' => 'array', 'images' => 'array', 'sale_terms' => 'array',
        'special_conditions' => 'array', 'views_count' => 'integer',
        'inquiries_count' => 'integer', 'offers_count' => 'integer',
        'parking_spaces' => 'integer', 'year_built' => 'integer',
        'property_size' => 'decimal:2', 'lot_size' => 'decimal:2',
        'bedrooms' => 'integer', 'bathrooms' => 'integer',
        'garage_spaces' => 'integer', 'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'sale_price', 'status', 'is_available'])
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

    public function inquiries(): HasMany
    {
        return $this->hasMany(PropertyInquiry::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(PropertyOffer::class);
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

    public function getFormattedPriceAttribute()
    {
        return 'Kshs. ' . number_format($this->sale_price, 2);
    }
}
