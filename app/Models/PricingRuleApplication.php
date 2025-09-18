<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PricingRuleApplication extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'pricing_rule_id',
        'property_id',
        'property_type',
        'applied_value',
        'applied_at',
        'applied_by',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'applied_value' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Get the pricing rule that owns this application
     */
    public function pricingRule(): BelongsTo
    {
        return $this->belongsTo(PricingRule::class);
    }

    /**
     * Get the user who applied this rule
     */
    public function appliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applied_by');
    }

    /**
     * Scope to get active applications
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get applications by property
     */
    public function scopeByProperty($query, $propertyId, $propertyType = null)
    {
        $query->where('property_id', $propertyId);
        
        if ($propertyType) {
            $query->where('property_type', $propertyType);
        }
        
        return $query;
    }
}
