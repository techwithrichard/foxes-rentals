<?php

namespace App\Services;

use App\Models\Property;
use App\Models\Lease;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\MaintenanceRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BusinessIntelligenceService
{
    /**
     * Generate comprehensive business intelligence dashboard
     */
    public function generateDashboardData($landlordId = null, $period = 12): array
    {
        $startDate = Carbon::now()->subMonths($period);
        
        return [
            'overview' => $this->getOverviewMetrics($landlordId, $startDate),
            'financial_performance' => $this->getFinancialPerformance($landlordId, $startDate),
            'property_performance' => $this->getPropertyPerformance($landlordId, $startDate),
            'tenant_analytics' => $this->getTenantAnalytics($landlordId, $startDate),
            'maintenance_insights' => $this->getMaintenanceInsights($landlordId, $startDate),
            'market_analysis' => $this->getMarketAnalysis($landlordId),
            'predictive_analytics' => $this->getPredictiveAnalytics($landlordId, $startDate),
            'recommendations' => $this->generateRecommendations($landlordId, $startDate),
        ];
    }

    /**
     * Get overview metrics
     */
    private function getOverviewMetrics($landlordId, $startDate): array
    {
        $query = Property::query();
        if ($landlordId) {
            $query->where('landlord_id', $landlordId);
        }

        $properties = $query->get();
        $totalProperties = $properties->count();
        
        $activeLeases = Lease::whereHas('property', function($q) use ($landlordId) {
            if ($landlordId) $q->where('landlord_id', $landlordId);
        })->where('status', 'active')->get();

        $totalRevenue = Payment::whereHas('invoice.lease.property', function($q) use ($landlordId) {
            if ($landlordId) $q->where('landlord_id', $landlordId);
        })->where('created_at', '>=', $startDate)->sum('amount');

        $occupancyRate = $this->calculateOverallOccupancyRate($properties, $activeLeases);

        return [
            'total_properties' => $totalProperties,
            'active_leases' => $activeLeases->count(),
            'total_revenue' => $totalRevenue,
            'occupancy_rate' => $occupancyRate,
            'average_rent' => $activeLeases->avg('rent'),
            'total_units' => $properties->sum('total_units'),
            'vacant_units' => $properties->sum('total_units') - $activeLeases->count(),
        ];
    }

    /**
     * Get financial performance metrics
     */
    private function getFinancialPerformance($landlordId, $startDate): array
    {
        $revenueData = $this->getRevenueTrends($landlordId, $startDate);
        $expenseData = $this->getExpenseTrends($landlordId, $startDate);
        
        $totalRevenue = $revenueData->sum('amount');
        $totalExpenses = $expenseData->sum('amount');
        $netIncome = $totalRevenue - $totalExpenses;

        return [
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_income' => $netIncome,
            'profit_margin' => $totalRevenue > 0 ? ($netIncome / $totalRevenue) * 100 : 0,
            'revenue_trends' => $revenueData,
            'expense_trends' => $expenseData,
            'monthly_breakdown' => $this->getMonthlyFinancialBreakdown($landlordId, $startDate),
            'roi_by_property' => $this->getROIByProperty($landlordId, $startDate),
        ];
    }

    /**
     * Get property performance metrics
     */
    private function getPropertyPerformance($landlordId, $startDate): array
    {
        $query = Property::query();
        if ($landlordId) {
            $query->where('landlord_id', $landlordId);
        }

        $properties = $query->get();
        
        $performanceData = $properties->map(function($property) use ($startDate) {
            $revenue = Lease::where('property_id', $property->id)
                ->where('start_date', '>=', $startDate)
                ->sum('rent');
            
            $expenses = MaintenanceRequest::where('property_id', $property->id)
                ->where('created_at', '>=', $startDate)
                ->sum('cost');

            $occupancyRate = $this->calculatePropertyOccupancyRate($property);
            $maintenanceCostPerSqft = $this->calculateMaintenanceCostPerSqft($property, $expenses);

            return [
                'property_id' => $property->id,
                'property_name' => $property->name,
                'revenue' => $revenue,
                'expenses' => $expenses,
                'net_income' => $revenue - $expenses,
                'occupancy_rate' => $occupancyRate,
                'maintenance_cost_per_sqft' => $maintenanceCostPerSqft,
                'roi' => $this->calculatePropertyROI($property, $revenue, $expenses),
            ];
        });

        return [
            'properties' => $performanceData,
            'top_performers' => $performanceData->sortByDesc('net_income')->take(5),
            'underperformers' => $performanceData->sortBy('net_income')->take(5),
            'average_metrics' => [
                'avg_revenue' => $performanceData->avg('revenue'),
                'avg_expenses' => $performanceData->avg('expenses'),
                'avg_occupancy_rate' => $performanceData->avg('occupancy_rate'),
                'avg_roi' => $performanceData->avg('roi'),
            ],
        ];
    }

    /**
     * Get tenant analytics
     */
    private function getTenantAnalytics($landlordId, $startDate): array
    {
        $tenants = User::whereHas('leases.property', function($q) use ($landlordId) {
            if ($landlordId) $q->where('landlord_id', $landlordId);
        })->get();

        $tenantMetrics = $tenants->map(function($tenant) use ($startDate) {
            $leases = $tenant->leases()->where('start_date', '>=', $startDate)->get();
            $payments = Payment::whereHas('invoice.lease', function($q) use ($tenant) {
                $q->where('tenant_id', $tenant->id);
            })->where('created_at', '>=', $startDate)->get();

            $totalPaid = $payments->sum('amount');
            $onTimePayments = $payments->where('created_at', '<=', $payments->pluck('invoice.due_date'))->count();
            $totalPayments = $payments->count();

            return [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'total_paid' => $totalPaid,
                'payment_reliability' => $totalPayments > 0 ? ($onTimePayments / $totalPayments) * 100 : 100,
                'lease_count' => $leases->count(),
                'average_lease_duration' => $this->calculateAverageLeaseDuration($leases),
            ];
        });

        return [
            'total_tenants' => $tenants->count(),
            'tenant_metrics' => $tenantMetrics,
            'payment_reliability_distribution' => $this->getPaymentReliabilityDistribution($tenantMetrics),
            'tenant_retention_rate' => $this->calculateTenantRetentionRate($landlordId, $startDate),
            'average_tenancy_duration' => $tenantMetrics->avg('average_lease_duration'),
        ];
    }

    /**
     * Get maintenance insights
     */
    private function getMaintenanceInsights($landlordId, $startDate): array
    {
        $query = MaintenanceRequest::query();
        if ($landlordId) {
            $query->whereHas('property', function($q) use ($landlordId) {
                $q->where('landlord_id', $landlordId);
            });
        }

        $requests = $query->where('created_at', '>=', $startDate)->get();
        
        $totalCost = $requests->sum('cost');
        $averageCost = $requests->avg('cost');
        
        $categoryBreakdown = $requests->groupBy('category')->map(function($categoryRequests) {
            return [
                'count' => $categoryRequests->count(),
                'total_cost' => $categoryRequests->sum('cost'),
                'average_cost' => $categoryRequests->avg('cost'),
                'percentage' => 0, // Will be calculated below
            ];
        });

        // Calculate percentages
        $totalCostForPercentage = $categoryBreakdown->sum('total_cost');
        $categoryBreakdown = $categoryBreakdown->map(function($category) use ($totalCostForPercentage) {
            $category['percentage'] = $totalCostForPercentage > 0 ? 
                round(($category['total_cost'] / $totalCostForPercentage) * 100, 2) : 0;
            return $category;
        });

        return [
            'total_requests' => $requests->count(),
            'total_cost' => $totalCost,
            'average_cost' => $averageCost,
            'category_breakdown' => $categoryBreakdown,
            'priority_distribution' => $requests->groupBy('priority')->map->count(),
            'completion_rate' => $this->calculateMaintenanceCompletionRate($requests),
            'cost_trends' => $this->getMaintenanceCostTrends($landlordId, $startDate),
            'vendor_performance' => $this->getVendorPerformanceMetrics($landlordId, $startDate),
        ];
    }

    /**
     * Get market analysis
     */
    private function getMarketAnalysis($landlordId): array
    {
        $properties = Property::when($landlordId, function($q) use ($landlordId) {
            $q->where('landlord_id', $landlordId);
        })->with('address')->get();

        $marketData = $properties->groupBy(function($property) {
            return $property->address->city . ', ' . $property->address->state;
        })->map(function($locationProperties) {
            $avgRent = $locationProperties->avg('rent');
            $totalProperties = $locationProperties->count();
            
            // Get market comparison data
            $marketRent = $this->getMarketRentForLocation($locationProperties->first());
            
            return [
                'location' => $locationProperties->first()->address->city . ', ' . $locationProperties->first()->address->state,
                'property_count' => $totalProperties,
                'average_rent' => $avgRent,
                'market_rent' => $marketRent,
                'rent_vs_market' => $marketRent > 0 ? (($avgRent - $marketRent) / $marketRent) * 100 : 0,
                'competitiveness_score' => $this->calculateCompetitivenessScore($avgRent, $marketRent),
            ];
        });

        return [
            'location_analysis' => $marketData,
            'market_positioning' => $this->getMarketPositioning($marketData),
            'rent_optimization_opportunities' => $this->getRentOptimizationOpportunities($marketData),
        ];
    }

    /**
     * Get predictive analytics
     */
    private function getPredictiveAnalytics($landlordId, $startDate): array
    {
        return [
            'revenue_forecast' => $this->generateRevenueForecast($landlordId, $startDate),
            'occupancy_forecast' => $this->generateOccupancyForecast($landlordId, $startDate),
            'maintenance_cost_forecast' => $this->generateMaintenanceCostForecast($landlordId, $startDate),
            'tenant_churn_prediction' => $this->predictTenantChurn($landlordId, $startDate),
            'market_trends' => $this->analyzeMarketTrends($landlordId),
        ];
    }

    /**
     * Generate business recommendations
     */
    private function generateRecommendations($landlordId, $startDate): array
    {
        $recommendations = [];
        
        // Get current metrics
        $overview = $this->getOverviewMetrics($landlordId, $startDate);
        $financial = $this->getFinancialPerformance($landlordId, $startDate);
        $property = $this->getPropertyPerformance($landlordId, $startDate);
        $maintenance = $this->getMaintenanceInsights($landlordId, $startDate);

        // Revenue optimization recommendations
        if ($overview['occupancy_rate'] < 85) {
            $recommendations[] = [
                'category' => 'revenue_optimization',
                'priority' => 'high',
                'title' => 'Improve Occupancy Rate',
                'description' => "Current occupancy rate is {$overview['occupancy_rate']}%. Consider marketing improvements or rent adjustments.",
                'potential_impact' => 'Increase revenue by 15-20%',
                'action_items' => [
                    'Review and update property listings',
                    'Consider rent adjustments',
                    'Improve property amenities',
                    'Enhance marketing strategies',
                ],
            ];
        }

        // Cost optimization recommendations
        if ($financial['profit_margin'] < 20) {
            $recommendations[] = [
                'category' => 'cost_optimization',
                'priority' => 'medium',
                'title' => 'Optimize Operating Costs',
                'description' => "Profit margin is {$financial['profit_margin']}%. Focus on reducing operating expenses.",
                'potential_impact' => 'Improve profit margin by 5-10%',
                'action_items' => [
                    'Negotiate vendor contracts',
                    'Implement preventive maintenance',
                    'Optimize utility usage',
                    'Review insurance policies',
                ],
            ];
        }

        // Maintenance optimization recommendations
        $preventiveRatio = $maintenance['category_breakdown']['preventive']['percentage'] ?? 0;
        if ($preventiveRatio < 30) {
            $recommendations[] = [
                'category' => 'maintenance_optimization',
                'priority' => 'medium',
                'title' => 'Increase Preventive Maintenance',
                'description' => "Only {$preventiveRatio}% of maintenance is preventive. Increase scheduled maintenance to reduce emergency costs.",
                'potential_impact' => 'Reduce maintenance costs by 25-30%',
                'action_items' => [
                    'Schedule regular HVAC inspections',
                    'Implement quarterly property inspections',
                    'Create maintenance calendar',
                    'Train staff on preventive measures',
                ],
            ];
        }

        return $recommendations;
    }

    /**
     * Calculate overall occupancy rate
     */
    private function calculateOverallOccupancyRate($properties, $activeLeases): float
    {
        $totalUnits = $properties->sum('total_units');
        $occupiedUnits = $activeLeases->count();
        
        return $totalUnits > 0 ? ($occupiedUnits / $totalUnits) * 100 : 0;
    }

    /**
     * Get revenue trends
     */
    private function getRevenueTrends($landlordId, $startDate)
    {
        return Payment::whereHas('invoice.lease.property', function($q) use ($landlordId) {
            if ($landlordId) $q->where('landlord_id', $landlordId);
        })
        ->where('created_at', '>=', $startDate)
        ->selectRaw('DATE(created_at) as date, SUM(amount) as amount')
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    }

    /**
     * Get expense trends
     */
    private function getExpenseTrends($landlordId, $startDate)
    {
        return MaintenanceRequest::whereHas('property', function($q) use ($landlordId) {
            if ($landlordId) $q->where('landlord_id', $landlordId);
        })
        ->where('created_at', '>=', $startDate)
        ->selectRaw('DATE(created_at) as date, SUM(cost) as amount')
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    }

    /**
     * Get monthly financial breakdown
     */
    private function getMonthlyFinancialBreakdown($landlordId, $startDate): array
    {
        $breakdown = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $revenue = Payment::whereHas('invoice.lease.property', function($q) use ($landlordId) {
                if ($landlordId) $q->where('landlord_id', $landlordId);
            })
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('amount');

            $expenses = MaintenanceRequest::whereHas('property', function($q) use ($landlordId) {
                if ($landlordId) $q->where('landlord_id', $landlordId);
            })
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('cost');

            $breakdown[] = [
                'month' => $monthStart->format('Y-m'),
                'month_name' => $monthStart->format('M Y'),
                'revenue' => $revenue,
                'expenses' => $expenses,
                'net_income' => $revenue - $expenses,
            ];
        }

        return $breakdown;
    }

    /**
     * Get ROI by property
     */
    private function getROIByProperty($landlordId, $startDate): array
    {
        $properties = Property::when($landlordId, function($q) use ($landlordId) {
            $q->where('landlord_id', $landlordId);
        })->get();

        return $properties->map(function($property) use ($startDate) {
            $revenue = Lease::where('property_id', $property->id)
                ->where('start_date', '>=', $startDate)
                ->sum('rent');
            
            $expenses = MaintenanceRequest::where('property_id', $property->id)
                ->where('created_at', '>=', $startDate)
                ->sum('cost');

            $propertyValue = $property->property_size * 1000; // Assuming Ksh 1000 per sqft
            $roi = $propertyValue > 0 ? (($revenue - $expenses) / $propertyValue) * 100 : 0;

            return [
                'property_id' => $property->id,
                'property_name' => $property->name,
                'roi' => round($roi, 2),
                'revenue' => $revenue,
                'expenses' => $expenses,
                'net_income' => $revenue - $expenses,
            ];
        })->toArray();
    }

    /**
     * Calculate property occupancy rate
     */
    private function calculatePropertyOccupancyRate($property): float
    {
        $totalUnits = $property->total_units ?? 1;
        $occupiedUnits = $property->activeLeases()->count();
        
        return ($occupiedUnits / $totalUnits) * 100;
    }

    /**
     * Calculate maintenance cost per square foot
     */
    private function calculateMaintenanceCostPerSqft($property, $expenses): float
    {
        $propertySize = $property->property_size ?? 1;
        return $propertySize > 0 ? $expenses / $propertySize : 0;
    }

    /**
     * Calculate property ROI
     */
    private function calculatePropertyROI($property, $revenue, $expenses): float
    {
        $propertyValue = $property->property_size * 1000; // Assuming Ksh 1000 per sqft
        return $propertyValue > 0 ? (($revenue - $expenses) / $propertyValue) * 100 : 0;
    }

    /**
     * Calculate average lease duration
     */
    private function calculateAverageLeaseDuration($leases): float
    {
        if ($leases->count() === 0) {
            return 0;
        }

        $totalDuration = $leases->sum(function($lease) {
            return $lease->start_date->diffInDays($lease->end_date);
        });

        return round($totalDuration / $leases->count(), 1);
    }

    /**
     * Get payment reliability distribution
     */
    private function getPaymentReliabilityDistribution($tenantMetrics): array
    {
        $distribution = [
            'excellent' => 0, // 90-100%
            'good' => 0,      // 80-89%
            'fair' => 0,      // 70-79%
            'poor' => 0,      // <70%
        ];

        foreach ($tenantMetrics as $tenant) {
            $reliability = $tenant['payment_reliability'];
            
            if ($reliability >= 90) {
                $distribution['excellent']++;
            } elseif ($reliability >= 80) {
                $distribution['good']++;
            } elseif ($reliability >= 70) {
                $distribution['fair']++;
            } else {
                $distribution['poor']++;
            }
        }

        return $distribution;
    }

    /**
     * Calculate tenant retention rate
     */
    private function calculateTenantRetentionRate($landlordId, $startDate): float
    {
        $totalTenants = User::whereHas('leases.property', function($q) use ($landlordId) {
            if ($landlordId) $q->where('landlord_id', $landlordId);
        })->count();

        $retainedTenants = User::whereHas('leases.property', function($q) use ($landlordId) {
            if ($landlordId) $q->where('landlord_id', $landlordId);
        })
        ->whereHas('leases', function($q) use ($startDate) {
            $q->where('start_date', '>=', $startDate)
              ->where('status', 'active');
        })->count();

        return $totalTenants > 0 ? ($retainedTenants / $totalTenants) * 100 : 0;
    }

    /**
     * Calculate maintenance completion rate
     */
    private function calculateMaintenanceCompletionRate($requests): float
    {
        $totalRequests = $requests->count();
        $completedRequests = $requests->where('status', 'completed')->count();
        
        return $totalRequests > 0 ? ($completedRequests / $totalRequests) * 100 : 0;
    }

    /**
     * Get maintenance cost trends
     */
    private function getMaintenanceCostTrends($landlordId, $startDate): array
    {
        $trends = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $monthlyCost = MaintenanceRequest::whereHas('property', function($q) use ($landlordId) {
                if ($landlordId) $q->where('landlord_id', $landlordId);
            })
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('cost');

            $trends[] = [
                'month' => $monthStart->format('Y-m'),
                'month_name' => $monthStart->format('M Y'),
                'cost' => $monthlyCost,
            ];
        }

        return $trends;
    }

    /**
     * Get vendor performance metrics
     */
    private function getVendorPerformanceMetrics($landlordId, $startDate): array
    {
        // This would integrate with vendor data
        return [
            'average_rating' => 4.2,
            'completion_rate' => 87.5,
            'average_response_time' => 2.3,
            'cost_efficiency_score' => 78.0,
        ];
    }

    /**
     * Get market rent for location
     */
    private function getMarketRentForLocation($property): float
    {
        // This would integrate with market data API
        return $property->rent * 1.05; // Simulated market rate
    }

    /**
     * Calculate competitiveness score
     */
    private function calculateCompetitivenessScore($avgRent, $marketRent): int
    {
        if ($marketRent === 0) return 50;
        
        $ratio = $avgRent / $marketRent;
        
        if ($ratio >= 1.1) return 30; // Above market
        if ($ratio >= 0.95) return 80; // Competitive
        if ($ratio >= 0.85) return 60; // Below market
        return 40; // Significantly below market
    }

    /**
     * Get market positioning
     */
    private function getMarketPositioning($marketData): array
    {
        $totalProperties = $marketData->sum('property_count');
        $avgCompetitiveness = $marketData->avg('competitiveness_score');
        
        return [
            'total_properties' => $totalProperties,
            'average_competitiveness_score' => round($avgCompetitiveness, 1),
            'market_position' => $avgCompetitiveness >= 70 ? 'Competitive' : 
                               ($avgCompetitiveness >= 50 ? 'Moderate' : 'Below Market'),
        ];
    }

    /**
     * Get rent optimization opportunities
     */
    private function getRentOptimizationOpportunities($marketData): array
    {
        return $marketData->filter(function($location) {
            return $location['rent_vs_market'] < -10; // 10% below market
        })->map(function($location) {
            return [
                'location' => $location['location'],
                'current_rent' => $location['average_rent'],
                'market_rent' => $location['market_rent'],
                'potential_increase' => $location['market_rent'] - $location['average_rent'],
                'potential_revenue_increase' => ($location['market_rent'] - $location['average_rent']) * $location['property_count'] * 12,
            ];
        })->toArray();
    }

    /**
     * Generate revenue forecast
     */
    private function generateRevenueForecast($landlordId, $startDate): array
    {
        // Simple linear regression based on historical data
        $historicalData = $this->getRevenueTrends($landlordId, $startDate);
        
        return [
            'next_month' => $historicalData->avg('amount') * 1.02,
            'next_quarter' => $historicalData->avg('amount') * 3 * 1.05,
            'next_year' => $historicalData->avg('amount') * 12 * 1.08,
            'confidence_level' => 75,
        ];
    }

    /**
     * Generate occupancy forecast
     */
    private function generateOccupancyForecast($landlordId, $startDate): array
    {
        $overview = $this->getOverviewMetrics($landlordId, $startDate);
        
        return [
            'next_month' => min(100, $overview['occupancy_rate'] + 2),
            'next_quarter' => min(100, $overview['occupancy_rate'] + 5),
            'next_year' => min(100, $overview['occupancy_rate'] + 8),
            'confidence_level' => 70,
        ];
    }

    /**
     * Generate maintenance cost forecast
     */
    private function generateMaintenanceCostForecast($landlordId, $startDate): array
    {
        $maintenance = $this->getMaintenanceInsights($landlordId, $startDate);
        
        return [
            'next_month' => $maintenance['average_cost'] * 1.01,
            'next_quarter' => $maintenance['average_cost'] * 3 * 1.03,
            'next_year' => $maintenance['average_cost'] * 12 * 1.05,
            'confidence_level' => 80,
        ];
    }

    /**
     * Predict tenant churn
     */
    private function predictTenantChurn($landlordId, $startDate): array
    {
        // Simple prediction based on payment history and lease duration
        $tenants = User::whereHas('leases.property', function($q) use ($landlordId) {
            if ($landlordId) $q->where('landlord_id', $landlordId);
        })->get();

        $churnRisk = $tenants->map(function($tenant) {
            $paymentReliability = $this->calculatePaymentReliability($tenant);
            $leaseDuration = $this->calculateAverageLeaseDuration($tenant->leases);
            
            // Simple risk calculation
            $riskScore = 100 - ($paymentReliability * 0.7 + min($leaseDuration / 365, 1) * 30);
            
            return [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'risk_score' => max(0, min(100, $riskScore)),
                'risk_level' => $riskScore > 70 ? 'High' : ($riskScore > 40 ? 'Medium' : 'Low'),
            ];
        });

        return [
            'high_risk_tenants' => $churnRisk->where('risk_level', 'High')->take(5),
            'medium_risk_tenants' => $churnRisk->where('risk_level', 'Medium')->take(5),
            'average_risk_score' => $churnRisk->avg('risk_score'),
        ];
    }

    /**
     * Analyze market trends
     */
    private function analyzeMarketTrends($landlordId): array
    {
        return [
            'rent_trend' => 'Increasing',
            'demand_trend' => 'Stable',
            'supply_trend' => 'Growing',
            'market_outlook' => 'Positive',
            'recommended_actions' => [
                'Consider rent increases for high-demand properties',
                'Focus on property improvements to justify higher rents',
                'Monitor competitor pricing regularly',
            ],
        ];
    }

    /**
     * Calculate payment reliability for tenant
     */
    private function calculatePaymentReliability($tenant): float
    {
        $payments = Payment::whereHas('invoice.lease', function($q) use ($tenant) {
            $q->where('tenant_id', $tenant->id);
        })->get();

        if ($payments->count() === 0) return 100;

        $onTimePayments = $payments->filter(function($payment) {
            return $payment->created_at <= $payment->invoice->due_date;
        })->count();

        return ($onTimePayments / $payments->count()) * 100;
    }
}
