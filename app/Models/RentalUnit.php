<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentalUnit extends Model
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $fillable = [
        'rental_property_id', 'unit_number', 'unit_name', 'floor_number',
        'rent_amount', 'deposit_amount', 'status', 'is_vacant',
        'bedrooms', 'bathrooms', 'square_footage', 'balcony',
        'parking_space', 'storage_unit', 'features', 'images',
        'notes', 'maintenance_notes'
    ];

    protected $casts = [
        'is_vacant' => 'boolean', 'balcony' => 'boolean',
        'parking_space' => 'boolean', 'storage_unit' => 'boolean',
        'rent_amount' => 'decimal:2', 'deposit_amount' => 'decimal:2',
        'square_footage' => 'decimal:2', 'features' => 'array',
        'images' => 'array', 'bedrooms' => 'integer',
        'bathrooms' => 'integer', 'floor_number' => 'integer'
    ];

    public function rentalProperty(): BelongsTo
    {
        return $this->belongsTo(RentalProperty::class);
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }

    public function activeLease(): HasMany
    {
        return $this->hasMany(Lease::class)->where('status', 'active');
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function scopeVacant($query)
    {
        return $query->where('is_vacant', true);
    }

    public function scopeOccupied($query)
    {
        return $query->where('is_vacant', false);
    }

    public function getFormattedRentAttribute()
    {
        return 'Kshs. ' . number_format($this->rent_amount, 2);
    }
}
