<?php

namespace App\Services;

use App\Models\Property;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\User;
use App\Models\Bill;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdvancedReportingService
{
    /**
     * Generate comprehensive financial report
     */
    public function generateFinancialReport(array $filters = []): array
    {
        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();
        $landlordId = $filters['landlord_id'] ?? null;

        $report = [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'summary' => $this->getFinancialSummary($startDate, $endDate, $landlordId),
            'revenue_breakdown' => $this->getRevenueBreakdown($startDate, $endDate, $landlordId),
            'expense_breakdown' => $this->getExpenseBreakdown($startDate, $endDate, $landlordId),
            'property_performance' => $this->getPropertyPerformance($startDate, $endDate, $landlordId),
            'tenant_analysis' => $this->getTenantAnalysis($startDate, $endDate, $landlordId),
            'payment_trends' => $this->getPaymentTrends($startDate, $endDate, $landlordId),
            'generated_at' => now()->toISOString()
        ];

        return $report;
    }

    /**
     * Generate property performance report
     */
    public function generatePropertyReport(array $filters = []): array
    {
        $propertyId = $filters['property_id'] ?? null;
        $startDate = $filters['start_date'] ?? now()->startOfYear();
        $endDate = $filters['end_date'] ?? now()->endOfYear();

        $report = [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'property_summary' => $this->getPropertySummary($propertyId, $startDate, $endDate),
            'occupancy_analysis' => $this->getOccupancyAnalysis($propertyId, $startDate, $endDate),
            'revenue_analysis' => $this->getPropertyRevenueAnalysis($propertyId, $startDate, $endDate),
            'maintenance_history' => $this->getMaintenanceHistory($propertyId, $startDate, $endDate),
            'tenant_history' => $this->getTenantHistory($propertyId, $startDate, $endDate),
            'market_comparison' => $this->getMarketComparison($propertyId),
            'generated_at' => now()->toISOString()
        ];

        return $report;
    }

    /**
     * Generate tenant analysis report
     */
    public function generateTenantReport(array $filters = []): array
    {
        $tenantId = $filters['tenant_id'] ?? null;
        $startDate = $filters['start_date'] ?? now()->startOfYear();
        $endDate = $filters['end_date'] ?? now()->endOfYear();

        $report = [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'tenant_profile' => $this->getTenantProfile($tenantId),
            'payment_history' => $this->getTenantPaymentHistory($tenantId, $startDate, $endDate),
            'lease_history' => $this->getTenantLeaseHistory($tenantId, $startDate, $endDate),
            'communication_log' => $this->getCommunicationLog($tenantId, $startDate, $endDate),
            'satisfaction_metrics' => $this->getSatisfactionMetrics($tenantId),
            'risk_assessment' => $this->getRiskAssessment($tenantId),
            'generated_at' => now()->toISOString()
        ];

        return $report;
    }

    /**
     * Generate occupancy report
     */
    public function generateOccupancyReport(array $filters = []): array
    {
        $startDate = $filters['start_date'] ?? now()->startOfYear();
        $endDate = $filters['end_date'] ?? now()->endOfYear();
        $propertyType = $filters['property_type'] ?? null;

        $report = [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'occupancy_summary' => $this->getOccupancySummary($startDate, $endDate, $propertyType),
            'vacancy_analysis' => $this->getVacancyAnalysis($startDate, $endDate, $propertyType),
            'turnover_analysis' => $this->getTurnoverAnalysis($startDate, $endDate, $propertyType),
            'rental_trends' => $this->getRentalTrends($startDate, $endDate, $propertyType),
            'market_analysis' => $this->getMarketAnalysis($startDate, $endDate, $propertyType),
            'generated_at' => now()->toISOString()
        ];

        return $report;
    }

    /**
     * Get financial summary
     */
    private function getFinancialSummary($startDate, $endDate, $landlordId = null): array
    {
        $query = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$startDate, $endDate]);

        if ($landlordId) {
            $query->whereHas('property', function ($q) use ($landlordId) {
                $q->where('landlord_id', $landlordId);
            });
        }

        $totalRevenue = $query->sum('amount');
        $totalPayments = $query->count();
        $averagePayment = $totalPayments > 0 ? $totalRevenue / $totalPayments : 0;

        return [
            'total_revenue' => $totalRevenue,
            'total_payments' => $totalPayments,
            'average_payment' => $averagePayment,
            'revenue_growth' => $this->calculateRevenueGrowth($startDate, $endDate, $landlordId),
            'collection_rate' => $this->calculateCollectionRate($startDate, $endDate, $landlordId)
        ];
    }

    /**
     * Get revenue breakdown
     */
    private function getRevenueBreakdown($startDate, $endDate, $landlordId = null): array
    {
        $query = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$startDate, $endDate]);

        if ($landlordId) {
            $query->whereHas('property', function ($q) use ($landlordId) {
                $q->where('landlord_id', $landlordId);
            });
        }

        return [
            'by_payment_method' => $query->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
                ->groupBy('payment_method')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->payment_method => [
                        'total' => $item->total,
                        'count' => $item->count,
                        'percentage' => 0 // Will be calculated
                    ]];
                }),
            'by_property_type' => $query->join('properties', 'payments.property_id', '=', 'properties.id')
                ->selectRaw('properties.type, SUM(payments.amount) as total, COUNT(*) as count')
                ->groupBy('properties.type')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->type => [
                        'total' => $item->total,
                        'count' => $item->count,
                        'percentage' => 0 // Will be calculated
                    ]];
                }),
            'monthly_trend' => $this->getMonthlyRevenueTrend($startDate, $endDate, $landlordId)
        ];
    }

    /**
     * Get expense breakdown
     */
    private function getExpenseBreakdown($startDate, $endDate, $landlordId = null): array
    {
        // This would include maintenance costs, utilities, etc.
        return [
            'maintenance_costs' => 0,
            'utility_costs' => 0,
            'management_fees' => 0,
            'marketing_costs' => 0,
            'total_expenses' => 0
        ];
    }

    /**
     * Get property performance
     */
    private function getPropertyPerformance($startDate, $endDate, $landlordId = null): array
    {
        $query = Property::query();

        if ($landlordId) {
            $query->where('landlord_id', $landlordId);
        }

        $properties = $query->with(['payments' => function ($q) use ($startDate, $endDate) {
            $q->where('status', 'completed')
              ->whereBetween('paid_at', [$startDate, $endDate]);
        }])->get();

        return $properties->map(function ($property) {
            $totalRevenue = $property->payments->sum('amount');
            $paymentCount = $property->payments->count();
            $averagePayment = $paymentCount > 0 ? $totalRevenue / $paymentCount : 0;

            return [
                'property_id' => $property->id,
                'name' => $property->name,
                'type' => $property->type,
                'total_revenue' => $totalRevenue,
                'payment_count' => $paymentCount,
                'average_payment' => $averagePayment,
                'occupancy_rate' => $this->calculateOccupancyRate($property, $startDate, $endDate),
                'rental_yield' => $this->calculateRentalYield($property, $startDate, $endDate)
            ];
        });
    }

    /**
     * Get tenant analysis
     */
    private function getTenantAnalysis($startDate, $endDate, $landlordId = null): array
    {
        $query = User::whereHas('leases');

        if ($landlordId) {
            $query->whereHas('leases.property', function ($q) use ($landlordId) {
                $q->where('landlord_id', $landlordId);
            });
        }

        $tenants = $query->with(['leases.property', 'payments' => function ($q) use ($startDate, $endDate) {
            $q->where('status', 'completed')
              ->whereBetween('paid_at', [$startDate, $endDate]);
        }])->get();

        return [
            'total_tenants' => $tenants->count(),
            'active_tenants' => $tenants->where('is_active', true)->count(),
            'payment_reliability' => $this->calculatePaymentReliability($tenants),
            'average_tenancy_duration' => $this->calculateAverageTenancyDuration($tenants),
            'tenant_satisfaction' => $this->calculateTenantSatisfaction($tenants)
        ];
    }

    /**
     * Get payment trends
     */
    private function getPaymentTrends($startDate, $endDate, $landlordId = null): array
    {
        $query = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$startDate, $endDate]);

        if ($landlordId) {
            $query->whereHas('property', function ($q) use ($landlordId) {
                $q->where('landlord_id', $landlordId);
            });
        }

        return [
            'daily_trends' => $query->selectRaw('DATE(paid_at) as date, SUM(amount) as total, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'weekly_trends' => $query->selectRaw('WEEK(paid_at) as week, SUM(amount) as total, COUNT(*) as count')
                ->groupBy('week')
                ->orderBy('week')
                ->get(),
            'monthly_trends' => $query->selectRaw('MONTH(paid_at) as month, SUM(amount) as total, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
        ];
    }

    /**
     * Calculate revenue growth
     */
    private function calculateRevenueGrowth($startDate, $endDate, $landlordId = null): float
    {
        $currentPeriod = $this->getFinancialSummary($startDate, $endDate, $landlordId);
        $previousPeriod = $this->getFinancialSummary(
            Carbon::parse($startDate)->subMonths(1),
            Carbon::parse($endDate)->subMonths(1),
            $landlordId
        );

        if ($previousPeriod['total_revenue'] == 0) {
            return 0;
        }

        return (($currentPeriod['total_revenue'] - $previousPeriod['total_revenue']) / $previousPeriod['total_revenue']) * 100;
    }

    /**
     * Calculate collection rate
     */
    private function calculateCollectionRate($startDate, $endDate, $landlordId = null): float
    {
        $totalBills = Bill::whereBetween('due_date', [$startDate, $endDate]);
        $collectedBills = Bill::whereBetween('due_date', [$startDate, $endDate])
            ->whereHas('payments', function ($q) {
                $q->where('status', 'completed');
            });

        if ($landlordId) {
            $totalBills->whereHas('lease.property', function ($q) use ($landlordId) {
                $q->where('landlord_id', $landlordId);
            });
            $collectedBills->whereHas('lease.property', function ($q) use ($landlordId) {
                $q->where('landlord_id', $landlordId);
            });
        }

        $totalCount = $totalBills->count();
        $collectedCount = $collectedBills->count();

        return $totalCount > 0 ? ($collectedCount / $totalCount) * 100 : 0;
    }

    /**
     * Get monthly revenue trend
     */
    private function getMonthlyRevenueTrend($startDate, $endDate, $landlordId = null): array
    {
        $query = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$startDate, $endDate]);

        if ($landlordId) {
            $query->whereHas('property', function ($q) use ($landlordId) {
                $q->where('landlord_id', $landlordId);
            });
        }

        return $query->selectRaw('YEAR(paid_at) as year, MONTH(paid_at) as month, SUM(amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT),
                    'revenue' => $item->total
                ];
            });
    }

    /**
     * Calculate occupancy rate
     */
    private function calculateOccupancyRate($property, $startDate, $endDate): float
    {
        $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $occupiedDays = $property->leases()
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->sum(DB::raw('DATEDIFF(LEAST(end_date, "' . $endDate . '"), GREATEST(start_date, "' . $startDate . '")) + 1'));

        return $totalDays > 0 ? ($occupiedDays / $totalDays) * 100 : 0;
    }

    /**
     * Calculate rental yield
     */
    private function calculateRentalYield($property, $startDate, $endDate): float
    {
        $totalRevenue = $property->payments()
            ->where('status', 'completed')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('amount');

        $propertyValue = $property->rent * 12; // Assuming annual rent as property value
        return $propertyValue > 0 ? ($totalRevenue / $propertyValue) * 100 : 0;
    }

    /**
     * Calculate payment reliability
     */
    private function calculatePaymentReliability($tenants): float
    {
        $totalPayments = $tenants->sum(function ($tenant) {
            return $tenant->payments->count();
        });

        $onTimePayments = $tenants->sum(function ($tenant) {
            return $tenant->payments->where('status', 'completed')->count();
        });

        return $totalPayments > 0 ? ($onTimePayments / $totalPayments) * 100 : 0;
    }

    /**
     * Calculate average tenancy duration
     */
    private function calculateAverageTenancyDuration($tenants): float
    {
        $totalDuration = $tenants->sum(function ($tenant) {
            return $tenant->leases->sum(function ($lease) {
                return Carbon::parse($lease->start_date)->diffInDays(Carbon::parse($lease->end_date));
            });
        });

        $totalLeases = $tenants->sum(function ($tenant) {
            return $tenant->leases->count();
        });

        return $totalLeases > 0 ? $totalDuration / $totalLeases : 0;
    }

    /**
     * Calculate tenant satisfaction
     */
    private function calculateTenantSatisfaction($tenants): float
    {
        // This would be based on feedback, reviews, etc.
        // For now, return a placeholder
        return 85.5; // Placeholder value
    }

    /**
     * Get property summary
     */
    private function getPropertySummary($propertyId, $startDate, $endDate): array
    {
        $property = Property::with(['leases', 'payments' => function ($q) use ($startDate, $endDate) {
            $q->where('status', 'completed')
              ->whereBetween('paid_at', [$startDate, $endDate]);
        }])->find($propertyId);

        if (!$property) {
            return [];
        }

        return [
            'property_id' => $property->id,
            'name' => $property->name,
            'type' => $property->type,
            'rent' => $property->rent,
            'status' => $property->status,
            'is_vacant' => $property->is_vacant,
            'total_revenue' => $property->payments->sum('amount'),
            'total_payments' => $property->payments->count(),
            'occupancy_rate' => $this->calculateOccupancyRate($property, $startDate, $endDate)
        ];
    }

    /**
     * Get occupancy analysis
     */
    private function getOccupancyAnalysis($propertyId, $startDate, $endDate): array
    {
        // Implementation for occupancy analysis
        return [
            'current_occupancy' => 0,
            'average_occupancy' => 0,
            'vacancy_periods' => [],
            'occupancy_trend' => []
        ];
    }

    /**
     * Get property revenue analysis
     */
    private function getPropertyRevenueAnalysis($propertyId, $startDate, $endDate): array
    {
        // Implementation for property revenue analysis
        return [
            'total_revenue' => 0,
            'monthly_revenue' => [],
            'revenue_growth' => 0,
            'revenue_per_sqft' => 0
        ];
    }

    /**
     * Get maintenance history
     */
    private function getMaintenanceHistory($propertyId, $startDate, $endDate): array
    {
        // Implementation for maintenance history
        return [
            'total_maintenance_cost' => 0,
            'maintenance_events' => [],
            'average_maintenance_cost' => 0
        ];
    }

    /**
     * Get tenant history
     */
    private function getTenantHistory($propertyId, $startDate, $endDate): array
    {
        // Implementation for tenant history
        return [
            'total_tenants' => 0,
            'tenant_turnover_rate' => 0,
            'average_tenancy_duration' => 0
        ];
    }

    /**
     * Get market comparison
     */
    private function getMarketComparison($propertyId): array
    {
        // Implementation for market comparison
        return [
            'market_rent' => 0,
            'rent_premium' => 0,
            'market_position' => 'average'
        ];
    }

    /**
     * Get tenant profile
     */
    private function getTenantProfile($tenantId): array
    {
        $tenant = User::with(['leases.property', 'payments'])->find($tenantId);

        if (!$tenant) {
            return [];
        }

        return [
            'tenant_id' => $tenant->id,
            'name' => $tenant->name,
            'email' => $tenant->email,
            'phone' => $tenant->phone,
            'current_property' => $tenant->leases->where('status', 'active')->first()?->property?->name,
            'total_payments' => $tenant->payments->count(),
            'total_amount_paid' => $tenant->payments->sum('amount'),
            'payment_reliability' => $this->calculatePaymentReliability(collect([$tenant]))
        ];
    }

    /**
     * Get tenant payment history
     */
    private function getTenantPaymentHistory($tenantId, $startDate, $endDate): array
    {
        $payments = Payment::where('tenant_id', $tenantId)
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->orderBy('paid_at')
            ->get();

        return $payments->map(function ($payment) {
            return [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'status' => $payment->status,
                'paid_at' => $payment->paid_at,
                'property_name' => $payment->property?->name
            ];
        });
    }

    /**
     * Get tenant lease history
     */
    private function getTenantLeaseHistory($tenantId, $startDate, $endDate): array
    {
        $leases = Lease::where('tenant_id', $tenantId)
            ->whereBetween('start_date', [$startDate, $endDate])
            ->with('property')
            ->orderBy('start_date')
            ->get();

        return $leases->map(function ($lease) {
            return [
                'lease_id' => $lease->id,
                'property_name' => $lease->property?->name,
                'start_date' => $lease->start_date,
                'end_date' => $lease->end_date,
                'rent' => $lease->rent,
                'status' => $lease->status
            ];
        });
    }

    /**
     * Get communication log
     */
    private function getCommunicationLog($tenantId, $startDate, $endDate): array
    {
        // Implementation for communication log
        return [];
    }

    /**
     * Get satisfaction metrics
     */
    private function getSatisfactionMetrics($tenantId): array
    {
        // Implementation for satisfaction metrics
        return [
            'overall_satisfaction' => 0,
            'communication_rating' => 0,
            'property_rating' => 0,
            'maintenance_rating' => 0
        ];
    }

    /**
     * Get risk assessment
     */
    private function getRiskAssessment($tenantId): array
    {
        // Implementation for risk assessment
        return [
            'risk_score' => 0,
            'risk_factors' => [],
            'recommendations' => []
        ];
    }

    /**
     * Get occupancy summary
     */
    private function getOccupancySummary($startDate, $endDate, $propertyType = null): array
    {
        $query = Property::query();

        if ($propertyType) {
            $query->where('type', $propertyType);
        }

        $properties = $query->get();

        return [
            'total_properties' => $properties->count(),
            'occupied_properties' => $properties->where('is_vacant', false)->count(),
            'vacant_properties' => $properties->where('is_vacant', true)->count(),
            'occupancy_rate' => $properties->count() > 0 ? ($properties->where('is_vacant', false)->count() / $properties->count()) * 100 : 0
        ];
    }

    /**
     * Get vacancy analysis
     */
    private function getVacancyAnalysis($startDate, $endDate, $propertyType = null): array
    {
        // Implementation for vacancy analysis
        return [
            'average_vacancy_duration' => 0,
            'vacancy_trends' => [],
            'vacancy_costs' => 0
        ];
    }

    /**
     * Get turnover analysis
     */
    private function getTurnoverAnalysis($startDate, $endDate, $propertyType = null): array
    {
        // Implementation for turnover analysis
        return [
            'turnover_rate' => 0,
            'average_tenancy_duration' => 0,
            'turnover_costs' => 0
        ];
    }

    /**
     * Get rental trends
     */
    private function getRentalTrends($startDate, $endDate, $propertyType = null): array
    {
        // Implementation for rental trends
        return [
            'rent_trends' => [],
            'market_rent' => 0,
            'rent_growth' => 0
        ];
    }

    /**
     * Get market analysis
     */
    private function getMarketAnalysis($startDate, $endDate, $propertyType = null): array
    {
        // Implementation for market analysis
        return [
            'market_conditions' => 'stable',
            'competition_analysis' => [],
            'market_opportunities' => []
        ];
    }
}
