<?php

namespace App\Services;

use App\Models\PropertyType;
use App\Models\PropertyAmenity;
use App\Models\PricingRule;
use App\Models\LeaseTemplate;
use App\Models\RentalProperty;
use App\Models\SaleProperty;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PropertySettingsService
{
    protected $cachePrefix = 'property_settings_';
    protected $cacheTtl = 3600; // 1 hour

    /**
     * Get comprehensive property statistics
     */
    public function getPropertyStatistics(): array
    {
        return Cache::remember($this->cachePrefix . 'statistics', $this->cacheTtl, function () {
            return [
                'property_types' => [
                    'total' => PropertyType::count(),
                    'active' => PropertyType::where('is_active', true)->count(),
                    'inactive' => PropertyType::where('is_active', false)->count(),
                    'with_properties' => PropertyType::has('rentalProperties')->orHas('saleProperties')->count()
                ],
                'amenities' => [
                    'total' => PropertyAmenity::count(),
                    'active' => PropertyAmenity::where('is_active', true)->count(),
                    'inactive' => PropertyAmenity::where('is_active', false)->count(),
                    'chargeable' => PropertyAmenity::where('is_chargeable', true)->count(),
                    'included' => PropertyAmenity::where('is_chargeable', false)->count(),
                    'categories' => PropertyAmenity::distinct('category')->count()
                ],
                'pricing_rules' => [
                    'total' => PricingRule::count(),
                    'active' => PricingRule::where('is_active', true)->count(),
                    'inactive' => PricingRule::where('is_active', false)->count(),
                    'by_type' => PricingRule::selectRaw('rule_type, COUNT(*) as count')
                        ->groupBy('rule_type')
                        ->pluck('count', 'rule_type')
                        ->toArray()
                ],
                'lease_templates' => [
                    'total' => LeaseTemplate::count(),
                    'active' => LeaseTemplate::where('is_active', true)->count(),
                    'inactive' => LeaseTemplate::where('is_active', false)->count(),
                    'by_type' => LeaseTemplate::selectRaw('template_type, COUNT(*) as count')
                        ->groupBy('template_type')
                        ->pluck('count', 'template_type')
                        ->toArray()
                ],
                'properties' => [
                    'rental_total' => RentalProperty::count(),
                    'rental_active' => RentalProperty::where('status', 'active')->count(),
                    'rental_vacant' => RentalProperty::where('status', 'vacant')->count(),
                    'sale_total' => SaleProperty::count(),
                    'sale_available' => SaleProperty::where('status', 'available')->count(),
                    'sale_sold' => SaleProperty::where('status', 'sold')->count()
                ],
                'usage_analytics' => [
                    'most_used_amenities' => $this->getMostUsedAmenities(),
                    'most_used_property_types' => $this->getMostUsedPropertyTypes(),
                    'most_used_pricing_rules' => $this->getMostUsedPricingRules(),
                    'most_used_lease_templates' => $this->getMostUsedLeaseTemplates()
                ]
            ];
        });
    }

    /**
     * Get most used amenities
     */
    protected function getMostUsedAmenities(): array
    {
        return PropertyAmenity::withCount('properties')
            ->orderByDesc('properties_count')
            ->limit(10)
            ->get()
            ->map(function ($amenity) {
                return [
                    'id' => $amenity->id,
                    'name' => $amenity->name,
                    'category' => $amenity->category,
                    'usage_count' => $amenity->properties_count,
                    'is_chargeable' => $amenity->is_chargeable,
                    'default_cost' => $amenity->default_cost
                ];
            })
            ->toArray();
    }

    /**
     * Get most used property types
     */
    protected function getMostUsedPropertyTypes(): array
    {
        return PropertyType::withCount(['rentalProperties', 'saleProperties'])
            ->get()
            ->map(function ($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'rental_count' => $type->rental_properties_count,
                    'sale_count' => $type->sale_properties_count,
                    'total_count' => $type->rental_properties_count + $type->sale_properties_count
                ];
            })
            ->sortByDesc('total_count')
            ->take(10)
            ->values()
            ->toArray();
    }

    /**
     * Get most used pricing rules
     */
    protected function getMostUsedPricingRules(): array
    {
        return PricingRule::withCount('properties')
            ->orderByDesc('properties_count')
            ->limit(10)
            ->get()
            ->map(function ($rule) {
                return [
                    'id' => $rule->id,
                    'name' => $rule->name,
                    'rule_type' => $rule->rule_type,
                    'calculation_method' => $rule->calculation_method,
                    'value' => $rule->value,
                    'usage_count' => $rule->properties_count
                ];
            })
            ->toArray();
    }

    /**
     * Get most used lease templates
     */
    protected function getMostUsedLeaseTemplates(): array
    {
        return LeaseTemplate::withCount('leases')
            ->orderByDesc('leases_count')
            ->limit(10)
            ->get()
            ->map(function ($template) {
                return [
                    'id' => $template->id,
                    'name' => $template->name,
                    'template_type' => $template->template_type,
                    'usage_count' => $template->leases_count,
                    'word_count' => $template->word_count
                ];
            })
            ->toArray();
    }

    /**
     * Get property settings by category
     */
    public function getPropertySettingsByCategory(string $category): array
    {
        return Cache::remember($this->cachePrefix . 'category_' . $category, $this->cacheTtl, function () use ($category) {
            switch ($category) {
                case 'property_types':
                    return PropertyType::ordered()->get()->toArray();
                case 'amenities':
                    return PropertyAmenity::ordered()->get()->toArray();
                case 'pricing_rules':
                    return PricingRule::ordered()->get()->toArray();
                case 'lease_templates':
                    return LeaseTemplate::ordered()->get()->toArray();
                default:
                    return [];
            }
        });
    }

    /**
     * Export property settings
     */
    public function exportPropertySettings(): array
    {
        return [
            'property_types' => PropertyType::all()->toArray(),
            'amenities' => PropertyAmenity::all()->toArray(),
            'pricing_rules' => PricingRule::all()->toArray(),
            'lease_templates' => LeaseTemplate::all()->toArray(),
            'exported_at' => now()->toISOString(),
            'exported_by' => auth()->id()
        ];
    }

    /**
     * Import property settings
     */
    public function importPropertySettings(array $settings, bool $overwrite = false, int $userId = null): array
    {
        $results = [
            'property_types' => ['imported' => 0, 'updated' => 0, 'errors' => 0],
            'amenities' => ['imported' => 0, 'updated' => 0, 'errors' => 0],
            'pricing_rules' => ['imported' => 0, 'updated' => 0, 'errors' => 0],
            'lease_templates' => ['imported' => 0, 'updated' => 0, 'errors' => 0]
        ];

        try {
            // Import Property Types
            if (isset($settings['property_types'])) {
                $results['property_types'] = $this->importPropertyTypes($settings['property_types'], $overwrite);
            }

            // Import Amenities
            if (isset($settings['amenities'])) {
                $results['amenities'] = $this->importAmenities($settings['amenities'], $overwrite);
            }

            // Import Pricing Rules
            if (isset($settings['pricing_rules'])) {
                $results['pricing_rules'] = $this->importPricingRules($settings['pricing_rules'], $overwrite);
            }

            // Import Lease Templates
            if (isset($settings['lease_templates'])) {
                $results['lease_templates'] = $this->importLeaseTemplates($settings['lease_templates'], $overwrite);
            }

            // Clear cache after import
            $this->clearCache();

            Log::info("Property settings imported by user {$userId}", $results);

            return $results;

        } catch (\Exception $e) {
            Log::error("Error importing property settings: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Import property types
     */
    protected function importPropertyTypes(array $propertyTypes, bool $overwrite): array
    {
        $results = ['imported' => 0, 'updated' => 0, 'errors' => 0];

        foreach ($propertyTypes as $data) {
            try {
                $existing = PropertyType::where('name', $data['name'])->first();

                if ($existing && $overwrite) {
                    $existing->update($data);
                    $results['updated']++;
                } elseif (!$existing) {
                    PropertyType::create($data);
                    $results['imported']++;
                }
            } catch (\Exception $e) {
                $results['errors']++;
                Log::error("Error importing property type {$data['name']}: " . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Import amenities
     */
    protected function importAmenities(array $amenities, bool $overwrite): array
    {
        $results = ['imported' => 0, 'updated' => 0, 'errors' => 0];

        foreach ($amenities as $data) {
            try {
                $existing = PropertyAmenity::where('name', $data['name'])->first();

                if ($existing && $overwrite) {
                    $existing->update($data);
                    $results['updated']++;
                } elseif (!$existing) {
                    PropertyAmenity::create($data);
                    $results['imported']++;
                }
            } catch (\Exception $e) {
                $results['errors']++;
                Log::error("Error importing amenity {$data['name']}: " . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Import pricing rules
     */
    protected function importPricingRules(array $pricingRules, bool $overwrite): array
    {
        $results = ['imported' => 0, 'updated' => 0, 'errors' => 0];

        foreach ($pricingRules as $data) {
            try {
                $existing = PricingRule::where('name', $data['name'])->first();

                if ($existing && $overwrite) {
                    $existing->update($data);
                    $results['updated']++;
                } elseif (!$existing) {
                    PricingRule::create($data);
                    $results['imported']++;
                }
            } catch (\Exception $e) {
                $results['errors']++;
                Log::error("Error importing pricing rule {$data['name']}: " . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Import lease templates
     */
    protected function importLeaseTemplates(array $leaseTemplates, bool $overwrite): array
    {
        $results = ['imported' => 0, 'updated' => 0, 'errors' => 0];

        foreach ($leaseTemplates as $data) {
            try {
                $existing = LeaseTemplate::where('name', $data['name'])->first();

                if ($existing && $overwrite) {
                    $existing->update($data);
                    $results['updated']++;
                } elseif (!$existing) {
                    LeaseTemplate::create($data);
                    $results['imported']++;
                }
            } catch (\Exception $e) {
                $results['errors']++;
                Log::error("Error importing lease template {$data['name']}: " . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Get property settings analytics
     */
    public function getPropertySettingsAnalytics(int $days = 30): array
    {
        return Cache::remember($this->cachePrefix . 'analytics_' . $days, $this->cacheTtl, function () use ($days) {
            $startDate = now()->subDays($days);

            return [
                'property_types_usage' => $this->getPropertyTypesUsage($startDate),
                'amenities_usage' => $this->getAmenitiesUsage($startDate),
                'pricing_rules_usage' => $this->getPricingRulesUsage($startDate),
                'lease_templates_usage' => $this->getLeaseTemplatesUsage($startDate),
                'category_distribution' => $this->getCategoryDistribution(),
                'monthly_trends' => $this->getMonthlyTrends($startDate)
            ];
        });
    }

    /**
     * Get property types usage
     */
    protected function getPropertyTypesUsage($startDate): array
    {
        return PropertyType::withCount(['rentalProperties' => function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }, 'saleProperties' => function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }])
            ->get()
            ->map(function ($type) {
                return [
                    'name' => $type->name,
                    'rental_count' => $type->rental_properties_count,
                    'sale_count' => $type->sale_properties_count,
                    'total_count' => $type->rental_properties_count + $type->sale_properties_count
                ];
            })
            ->sortByDesc('total_count')
            ->values()
            ->toArray();
    }

    /**
     * Get amenities usage
     */
    protected function getAmenitiesUsage($startDate): array
    {
        return PropertyAmenity::withCount(['properties' => function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }])
            ->orderByDesc('properties_count')
            ->limit(20)
            ->get()
            ->map(function ($amenity) {
                return [
                    'name' => $amenity->name,
                    'category' => $amenity->category,
                    'usage_count' => $amenity->properties_count,
                    'is_chargeable' => $amenity->is_chargeable
                ];
            })
            ->toArray();
    }

    /**
     * Get pricing rules usage
     */
    protected function getPricingRulesUsage($startDate): array
    {
        return PricingRule::withCount(['properties' => function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }])
            ->orderByDesc('properties_count')
            ->get()
            ->map(function ($rule) {
                return [
                    'name' => $rule->name,
                    'rule_type' => $rule->rule_type,
                    'usage_count' => $rule->properties_count,
                    'calculation_method' => $rule->calculation_method
                ];
            })
            ->toArray();
    }

    /**
     * Get lease templates usage
     */
    protected function getLeaseTemplatesUsage($startDate): array
    {
        return LeaseTemplate::withCount(['leases' => function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }])
            ->orderByDesc('leases_count')
            ->get()
            ->map(function ($template) {
                return [
                    'name' => $template->name,
                    'template_type' => $template->template_type,
                    'usage_count' => $template->leases_count,
                    'word_count' => $template->word_count
                ];
            })
            ->toArray();
    }

    /**
     * Get category distribution
     */
    protected function getCategoryDistribution(): array
    {
        return [
            'amenities_by_category' => PropertyAmenity::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->pluck('count', 'category')
                ->toArray(),
            'property_types_by_usage' => PropertyType::withCount(['rentalProperties', 'saleProperties'])
                ->get()
                ->mapWithKeys(function ($type) {
                    return [$type->name => $type->rental_properties_count + $type->sale_properties_count];
                })
                ->toArray()
        ];
    }

    /**
     * Get monthly trends
     */
    protected function getMonthlyTrends($startDate): array
    {
        return [
            'property_types_created' => PropertyType::where('created_at', '>=', $startDate)
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray(),
            'amenities_created' => PropertyAmenity::where('created_at', '>=', $startDate)
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray()
        ];
    }

    /**
     * Clear property settings cache
     */
    public function clearCache(): void
    {
        $cacheKeys = [
            $this->cachePrefix . 'statistics',
            $this->cachePrefix . 'analytics_30',
            $this->cachePrefix . 'category_property_types',
            $this->cachePrefix . 'category_amenities',
            $this->cachePrefix . 'category_pricing_rules',
            $this->cachePrefix . 'category_lease_templates'
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Validate property settings data
     */
    public function validatePropertySettings(array $data): array
    {
        $errors = [];

        // Validate property types
        if (isset($data['property_types'])) {
            foreach ($data['property_types'] as $index => $type) {
                if (empty($type['name'])) {
                    $errors[] = "Property type at index {$index} is missing a name";
                }
            }
        }

        // Validate amenities
        if (isset($data['amenities'])) {
            foreach ($data['amenities'] as $index => $amenity) {
                if (empty($amenity['name'])) {
                    $errors[] = "Amenity at index {$index} is missing a name";
                }
                if (empty($amenity['category'])) {
                    $errors[] = "Amenity at index {$index} is missing a category";
                }
            }
        }

        // Validate pricing rules
        if (isset($data['pricing_rules'])) {
            foreach ($data['pricing_rules'] as $index => $rule) {
                if (empty($rule['name'])) {
                    $errors[] = "Pricing rule at index {$index} is missing a name";
                }
                if (empty($rule['rule_type'])) {
                    $errors[] = "Pricing rule at index {$index} is missing a rule type";
                }
                if (empty($rule['calculation_method'])) {
                    $errors[] = "Pricing rule at index {$index} is missing a calculation method";
                }
            }
        }

        // Validate lease templates
        if (isset($data['lease_templates'])) {
            foreach ($data['lease_templates'] as $index => $template) {
                if (empty($template['name'])) {
                    $errors[] = "Lease template at index {$index} is missing a name";
                }
                if (empty($template['content'])) {
                    $errors[] = "Lease template at index {$index} is missing content";
                }
            }
        }

        return $errors;
    }
}
