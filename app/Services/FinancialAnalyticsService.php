<?php

namespace App\Services;

use App\Models\Property;
use App\Models\RentalProperty;
use App\Models\SaleProperty;
use App\Models\LeaseProperty;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\MaintenanceRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialAnalyticsService
{
    /**
     * Calculate Return on Investment (ROI) for a property
     */
    public function getPropertyROI($propertyId, $period = 12): array
    {
        $startDate = Carbon::now()->subMonths($period);
        
        // Get total revenue from leases
        $totalRevenue = Lease::whereHas('property', function($query) use ($propertyId) {
            $query->where('id', $propertyId);
        })
        ->where('start_date', '>=', $startDate)
        ->sum(DB::raw('rent * TIMESTAMPDIFF(MONTH, start_date, LEAST(end_date, NOW()))'));

        // Get total expenses (maintenance, utilities, etc.)
        $totalExpenses = MaintenanceRequest::where('property_id', $propertyId)
            ->where('created_at', '>=', $startDate)
            ->sum('cost');

        // Get property value (if available)
        $property = Property::find($propertyId);
        $propertyValue = $property->property_size * 1000; // Assuming Ksh 1000 per sqft

        $netIncome = $totalRevenue - $totalExpenses;
        $roi = $propertyValue > 0 ? ($netIncome / $propertyValue) * 100 : 0;

        return [
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_income' => $netIncome,
            'property_value' => $propertyValue,
            'roi_percentage' => round($roi, 2),
            'period_months' => $period,
        ];
    }

    /**
     * Calculate occupancy revenue for a property
     */
    public function getOccupancyRevenue($propertyId, $period = 12): array
    {
        $startDate = Carbon::now()->subMonths($period);
        
        $leases = Lease::whereHas('property', function($query) use ($propertyId) {
            $query->where('id', $propertyId);
        })
        ->where('start_date', '>=', $startDate)
        ->with('tenant')
        ->get();

        $totalRevenue = 0;
        $occupancyDays = 0;
        $totalDays = $period * 30; // Approximate days in period

        foreach ($leases as $lease) {
            $leaseDays = $lease->start_date->diffInDays($lease->end_date ?? Carbon::now());
            $occupancyDays += $leaseDays;
            $totalRevenue += $lease->rent * ceil($leaseDays / 30); // Monthly rent calculation
        }

        $occupancyRate = $totalDays > 0 ? ($occupancyDays / $totalDays) * 100 : 0;
        $revenuePerDay = $occupancyDays > 0 ? $totalRevenue / $occupancyDays : 0;

        return [
            'total_revenue' => $totalRevenue,
            'occupancy_days' => $occupancyDays,
            'total_days' => $totalDays,
            'occupancy_rate' => round($occupancyRate, 2),
            'revenue_per_day' => round($revenuePerDay, 2),
            'active_leases' => $leases->count(),
        ];
    }

    /**
     * Calculate maintenance costs for a property
     */
    public function getMaintenanceCosts($propertyId, $period = 12): array
    {
        $startDate = Carbon::now()->subMonths($period);
        
        $maintenanceRequests = MaintenanceRequest::where('property_id', $propertyId)
            ->where('created_at', '>=', $startDate)
            ->get();

        $totalCost = $maintenanceRequests->sum('cost');
        $averageCost = $maintenanceRequests->count() > 0 ? $totalCost / $maintenanceRequests->count() : 0;
        
        // Group by category if available
        $costsByCategory = $maintenanceRequests->groupBy('category')
            ->map(function($requests) {
                return [
                    'count' => $requests->count(),
                    'total_cost' => $requests->sum('cost'),
                    'average_cost' => $requests->avg('cost'),
                ];
            });

        return [
            'total_cost' => $totalCost,
            'request_count' => $maintenanceRequests->count(),
            'average_cost_per_request' => round($averageCost, 2),
            'costs_by_category' => $costsByCategory,
            'period_months' => $period,
        ];
    }

    /**
     * Get market comparison data
     */
    public function getMarketComparison($propertyId): array
    {
        $property = Property::with('address')->find($propertyId);
        
        if (!$property || !$property->address) {
            return ['error' => 'Property or address not found'];
        }

        // Get similar properties in the same area
        $similarProperties = Property::whereHas('address', function($query) use ($property) {
            $query->where('city', $property->address->city)
                  ->where('state', $property->address->state);
        })
        ->where('id', '!=', $propertyId)
        ->where('status', 'active')
        ->get();

        $marketRent = $similarProperties->avg('rent');
        $propertyRent = $property->rent;
        
        $rentComparison = $marketRent > 0 ? (($propertyRent - $marketRent) / $marketRent) * 100 : 0;

        return [
            'property_rent' => $propertyRent,
            'market_average_rent' => round($marketRent, 2),
            'rent_comparison_percentage' => round($rentComparison, 2),
            'similar_properties_count' => $similarProperties->count(),
            'market_position' => $rentComparison > 10 ? 'Above Market' : 
                               ($rentComparison < -10 ? 'Below Market' : 'Market Rate'),
        ];
    }

    /**
     * Generate comprehensive financial report for a property
     */
    public function generatePropertyFinancialReport($propertyId, $period = 12): array
    {
        $roi = $this->getPropertyROI($propertyId, $period);
        $occupancy = $this->getOccupancyRevenue($propertyId, $period);
        $maintenance = $this->getMaintenanceCosts($propertyId, $period);
        $market = $this->getMarketComparison($propertyId);

        return [
            'property_id' => $propertyId,
            'period_months' => $period,
            'report_date' => Carbon::now()->toDateString(),
            'roi_analysis' => $roi,
            'occupancy_analysis' => $occupancy,
            'maintenance_analysis' => $maintenance,
            'market_analysis' => $market,
            'summary' => [
                'total_revenue' => $roi['total_revenue'],
                'total_expenses' => $roi['total_expenses'],
                'net_income' => $roi['net_income'],
                'occupancy_rate' => $occupancy['occupancy_rate'],
                'roi_percentage' => $roi['roi_percentage'],
                'maintenance_cost' => $maintenance['total_cost'],
            ]
        ];
    }

    /**
     * Get portfolio-wide financial analytics
     */
    public function getPortfolioAnalytics($landlordId = null, $period = 12): array
    {
        $query = Property::query();
        
        if ($landlordId) {
            $query->where('landlord_id', $landlordId);
        }

        $properties = $query->get();
        $totalProperties = $properties->count();
        
        $totalRevenue = 0;
        $totalExpenses = 0;
        $totalMaintenanceCost = 0;
        $occupiedProperties = 0;

        foreach ($properties as $property) {
            $roi = $this->getPropertyROI($property->id, $period);
            $occupancy = $this->getOccupancyRevenue($property->id, $period);
            $maintenance = $this->getMaintenanceCosts($property->id, $period);

            $totalRevenue += $roi['total_revenue'];
            $totalExpenses += $roi['total_expenses'];
            $totalMaintenanceCost += $maintenance['total_cost'];
            
            if ($occupancy['occupancy_rate'] > 0) {
                $occupiedProperties++;
            }
        }

        $averageOccupancyRate = $properties->count() > 0 ? 
            $properties->sum(function($property) use ($period) {
                $occupancy = $this->getOccupancyRevenue($property->id, $period);
                return $occupancy['occupancy_rate'];
            }) / $properties->count() : 0;

        return [
            'total_properties' => $totalProperties,
            'occupied_properties' => $occupiedProperties,
            'vacancy_rate' => $totalProperties > 0 ? 
                (($totalProperties - $occupiedProperties) / $totalProperties) * 100 : 0,
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_income' => $totalRevenue - $totalExpenses,
            'total_maintenance_cost' => $totalMaintenanceCost,
            'average_occupancy_rate' => round($averageOccupancyRate, 2),
            'period_months' => $period,
        ];
    }

    /**
     * Get revenue trends over time
     */
    public function getRevenueTrends($propertyId, $period = 12): array
    {
        $startDate = Carbon::now()->subMonths($period);
        
        $monthlyRevenue = [];
        for ($i = $period - 1; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $monthlyRevenue[] = [
                'month' => $monthStart->format('Y-m'),
                'month_name' => $monthStart->format('M Y'),
                'revenue' => Lease::whereHas('property', function($query) use ($propertyId) {
                    $query->where('id', $propertyId);
                })
                ->whereBetween('start_date', [$monthStart, $monthEnd])
                ->sum('rent'),
            ];
        }

        return $monthlyRevenue;
    }
}
