<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PricingRule extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'rule_type',
        'conditions',
        'calculation_method',
        'value',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'conditions' => 'array',
        'is_active' => 'boolean',
        'value' => 'decimal:2',
        'sort_order' => 'integer'
    ];

    /**
     * Get properties that use this pricing rule
     */
    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(RentalProperty::class, 'property_pricing_rules')
            ->withPivot('applied_value', 'applied_at')
            ->withTimestamps();
    }

    /**
     * Get pricing rule applications (history)
     */
    public function applications(): HasMany
    {
        return $this->hasMany(PricingRuleApplication::class);
    }

    /**
     * Scope to get active pricing rules
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get pricing rules by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('rule_type', $type);
    }

    /**
     * Scope to get pricing rules by calculation method
     */
    public function scopeByCalculationMethod($query, string $method)
    {
        return $query->where('calculation_method', $method);
    }

    /**
     * Scope to order by type and sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('rule_type')->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the rule type display name
     */
    public function getRuleTypeDisplayNameAttribute(): string
    {
        $types = [
            'commission' => 'Commission',
            'late_fee' => 'Late Fee',
            'deposit' => 'Deposit',
            'renewal_fee' => 'Renewal Fee',
            'maintenance_fee' => 'Maintenance Fee',
            'processing_fee' => 'Processing Fee',
            'utility_fee' => 'Utility Fee',
            'parking_fee' => 'Parking Fee',
            'pet_fee' => 'Pet Fee',
            'cleaning_fee' => 'Cleaning Fee'
        ];

        return $types[$this->rule_type] ?? ucfirst(str_replace('_', ' ', $this->rule_type));
    }

    /**
     * Get the calculation method display name
     */
    public function getCalculationMethodDisplayNameAttribute(): string
    {
        $methods = [
            'percentage' => 'Percentage',
            'fixed_amount' => 'Fixed Amount',
            'sliding_scale' => 'Sliding Scale',
            'per_square_foot' => 'Per Square Foot',
            'per_unit' => 'Per Unit',
            'per_room' => 'Per Room'
        ];

        return $methods[$this->calculation_method] ?? ucfirst(str_replace('_', ' ', $this->calculation_method));
    }

    /**
     * Get the formatted value
     */
    public function getFormattedValueAttribute(): string
    {
        switch ($this->calculation_method) {
            case 'percentage':
                return $this->value . '%';
            case 'fixed_amount':
                return 'KSh ' . number_format($this->value, 2);
            case 'per_square_foot':
                return 'KSh ' . number_format($this->value, 2) . '/sq ft';
            case 'per_unit':
                return 'KSh ' . number_format($this->value, 2) . '/unit';
            case 'per_room':
                return 'KSh ' . number_format($this->value, 2) . '/room';
            default:
                return 'KSh ' . number_format($this->value, 2);
        }
    }

    /**
     * Get the icon for the rule type
     */
    public function getIconAttribute(): string
    {
        $icons = [
            'commission' => 'ni-money',
            'late_fee' => 'ni-alert-circle',
            'deposit' => 'ni-shield-check',
            'renewal_fee' => 'ni-refresh',
            'maintenance_fee' => 'ni-tools',
            'processing_fee' => 'ni-file-text',
            'utility_fee' => 'ni-power',
            'parking_fee' => 'ni-car',
            'pet_fee' => 'ni-heart',
            'cleaning_fee' => 'ni-home'
        ];

        return $icons[$this->rule_type] ?? 'ni-star';
    }

    /**
     * Apply the pricing rule to a given amount and context
     */
    public function applyRule(float $baseAmount, array $context = []): float
    {
        if (!$this->is_active) {
            return 0;
        }

        // Check if conditions are met
        if (!$this->meetsConditions($context)) {
            return 0;
        }

        switch ($this->calculation_method) {
            case 'percentage':
                return ($baseAmount * $this->value) / 100;

            case 'fixed_amount':
                return $this->value;

            case 'sliding_scale':
                return $this->calculateSlidingScale($baseAmount, $context);

            case 'per_square_foot':
                $area = $context['area'] ?? 1;
                return $this->value * $area;

            case 'per_unit':
                $units = $context['units'] ?? 1;
                return $this->value * $units;

            case 'per_room':
                $rooms = $context['rooms'] ?? 1;
                return $this->value * $rooms;

            default:
                return 0;
        }
    }

    /**
     * Check if the rule conditions are met
     */
    protected function meetsConditions(array $context): bool
    {
        if (empty($this->conditions)) {
            return true;
        }

        foreach ($this->conditions as $condition) {
            $field = $condition['field'] ?? '';
            $operator = $condition['operator'] ?? '=';
            $value = $condition['value'] ?? '';

            if (!isset($context[$field])) {
                continue;
            }

            $contextValue = $context[$field];

            switch ($operator) {
                case '=':
                    if ($contextValue != $value) return false;
                    break;
                case '!=':
                    if ($contextValue == $value) return false;
                    break;
                case '>':
                    if ($contextValue <= $value) return false;
                    break;
                case '>=':
                    if ($contextValue < $value) return false;
                    break;
                case '<':
                    if ($contextValue >= $value) return false;
                    break;
                case '<=':
                    if ($contextValue > $value) return false;
                    break;
                case 'in':
                    if (!in_array($contextValue, (array)$value)) return false;
                    break;
                case 'not_in':
                    if (in_array($contextValue, (array)$value)) return false;
                    break;
            }
        }

        return true;
    }

    /**
     * Calculate sliding scale pricing
     */
    protected function calculateSlidingScale(float $baseAmount, array $context): float
    {
        $scale = $this->conditions['scale'] ?? [];
        
        if (empty($scale)) {
            return $this->value;
        }

        foreach ($scale as $tier) {
            $min = $tier['min'] ?? 0;
            $max = $tier['max'] ?? PHP_FLOAT_MAX;
            $rate = $tier['rate'] ?? 0;

            if ($baseAmount >= $min && $baseAmount < $max) {
                return $baseAmount * ($rate / 100);
            }
        }

        return $this->value;
    }

    /**
     * Get usage statistics
     */
    public function getUsageStatsAttribute(): array
    {
        return [
            'total_applications' => $this->applications()->count(),
            'total_amount_generated' => $this->applications()->sum('calculated_amount'),
            'average_amount' => $this->applications()->avg('calculated_amount') ?? 0,
            'last_applied' => $this->applications()->latest()->first()?->created_at,
            'properties_using' => $this->properties()->count()
        ];
    }

    /**
     * Get suggested rule types
     */
    public static function getSuggestedRuleTypes(): array
    {
        return [
            'commission' => 'Commission',
            'late_fee' => 'Late Fee',
            'deposit' => 'Deposit',
            'renewal_fee' => 'Renewal Fee',
            'maintenance_fee' => 'Maintenance Fee',
            'processing_fee' => 'Processing Fee',
            'utility_fee' => 'Utility Fee',
            'parking_fee' => 'Parking Fee',
            'pet_fee' => 'Pet Fee',
            'cleaning_fee' => 'Cleaning Fee'
        ];
    }

    /**
     * Get suggested calculation methods
     */
    public static function getSuggestedCalculationMethods(): array
    {
        return [
            'percentage' => 'Percentage',
            'fixed_amount' => 'Fixed Amount',
            'sliding_scale' => 'Sliding Scale',
            'per_square_foot' => 'Per Square Foot',
            'per_unit' => 'Per Unit',
            'per_room' => 'Per Room'
        ];
    }

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($rule) {
            if (is_null($rule->sort_order)) {
                $rule->sort_order = static::where('rule_type', $rule->rule_type)->max('sort_order') + 1;
            }
        });
    }
}
