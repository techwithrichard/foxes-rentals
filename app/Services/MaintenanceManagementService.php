<?php

namespace App\Services;

use App\Models\Property;
use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaintenanceManagementService
{
    /**
     * Schedule preventive maintenance for a property
     */
    public function schedulePreventiveMaintenance($propertyId): array
    {
        $property = Property::findOrFail($propertyId);
        
        $preventiveTasks = [
            'HVAC Inspection' => [
                'frequency' => 'quarterly',
                'estimated_duration' => 2,
                'priority' => 'medium',
                'category' => 'hvac',
                'description' => 'Regular HVAC system inspection and maintenance',
            ],
            'Plumbing Check' => [
                'frequency' => 'monthly',
                'estimated_duration' => 1,
                'priority' => 'medium',
                'category' => 'plumbing',
                'description' => 'Check for leaks and water pressure issues',
            ],
            'Electrical Inspection' => [
                'frequency' => 'annually',
                'estimated_duration' => 3,
                'priority' => 'high',
                'category' => 'electrical',
                'description' => 'Annual electrical system safety inspection',
            ],
            'Pest Control' => [
                'frequency' => 'quarterly',
                'estimated_duration' => 1,
                'priority' => 'medium',
                'category' => 'pest_control',
                'description' => 'Regular pest control treatment',
            ],
            'Fire Safety Check' => [
                'frequency' => 'monthly',
                'estimated_duration' => 1,
                'priority' => 'high',
                'category' => 'safety',
                'description' => 'Fire extinguisher and smoke detector inspection',
            ],
        ];

        $scheduledTasks = [];
        
        foreach ($preventiveTasks as $taskName => $taskData) {
            $nextDueDate = $this->calculateNextDueDate($taskData['frequency']);
            
            // Check if task is already scheduled
            $existingTask = MaintenanceRequest::where('property_id', $propertyId)
                ->where('title', $taskName)
                ->where('type', 'preventive')
                ->where('scheduled_date', '>=', Carbon::now())
                ->first();

            if (!$existingTask) {
                $scheduledTasks[] = MaintenanceRequest::create([
                    'property_id' => $propertyId,
                    'title' => $taskName,
                    'description' => $taskData['description'],
                    'category' => $taskData['category'],
                    'priority' => $taskData['priority'],
                    'type' => 'preventive',
                    'status' => 'scheduled',
                    'scheduled_date' => $nextDueDate,
                    'estimated_duration' => $taskData['estimated_duration'],
                    'estimated_cost' => $this->estimatePreventiveCost($taskData['category']),
                    'frequency' => $taskData['frequency'],
                ]);
            }
        }

        return [
            'success' => true,
            'message' => 'Preventive maintenance scheduled successfully',
            'scheduled_tasks' => $scheduledTasks,
            'total_tasks' => count($scheduledTasks),
        ];
    }

    /**
     * Assign vendor to maintenance request
     */
    public function assignVendor($requestId, $vendorId): array
    {
        $request = MaintenanceRequest::findOrFail($requestId);
        $vendor = Vendor::findOrFail($vendorId);

        $request->update([
            'vendor_id' => $vendorId,
            'assigned_at' => Carbon::now(),
            'status' => 'assigned',
        ]);

        // Log the assignment
        Log::info('Vendor assigned to maintenance request', [
            'request_id' => $requestId,
            'vendor_id' => $vendorId,
            'vendor_name' => $vendor->name,
            'property_id' => $request->property_id,
        ]);

        return [
            'success' => true,
            'message' => "Vendor {$vendor->name} assigned successfully",
            'request' => $request->fresh(['vendor']),
        ];
    }

    /**
     * Track maintenance costs for a property
     */
    public function trackMaintenanceCosts($propertyId, $period = 12): array
    {
        $startDate = Carbon::now()->subMonths($period);
        
        $maintenanceRequests = MaintenanceRequest::where('property_id', $propertyId)
            ->where('created_at', '>=', $startDate)
            ->get();

        $totalCost = $maintenanceRequests->sum('cost');
        $averageCost = $maintenanceRequests->count() > 0 ? $totalCost / $maintenanceRequests->count() : 0;
        
        // Group by category
        $costsByCategory = $maintenanceRequests->groupBy('category')
            ->map(function($requests) {
                return [
                    'count' => $requests->count(),
                    'total_cost' => $requests->sum('cost'),
                    'average_cost' => $requests->avg('cost'),
                    'percentage' => 0, // Will be calculated below
                ];
            });

        // Calculate percentages
        $totalCostForPercentage = $costsByCategory->sum('total_cost');
        $costsByCategory = $costsByCategory->map(function($category) use ($totalCostForPercentage) {
            $category['percentage'] = $totalCostForPercentage > 0 ? 
                round(($category['total_cost'] / $totalCostForPercentage) * 100, 2) : 0;
            return $category;
        });

        // Group by priority
        $costsByPriority = $maintenanceRequests->groupBy('priority')
            ->map(function($requests) {
                return [
                    'count' => $requests->count(),
                    'total_cost' => $requests->sum('cost'),
                    'average_cost' => $requests->avg('cost'),
                ];
            });

        // Monthly breakdown
        $monthlyCosts = [];
        for ($i = $period - 1; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $monthlyCost = $maintenanceRequests
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('cost');

            $monthlyCosts[] = [
                'month' => $monthStart->format('Y-m'),
                'month_name' => $monthStart->format('M Y'),
                'cost' => $monthlyCost,
            ];
        }

        return [
            'total_cost' => $totalCost,
            'request_count' => $maintenanceRequests->count(),
            'average_cost_per_request' => round($averageCost, 2),
            'costs_by_category' => $costsByCategory,
            'costs_by_priority' => $costsByPriority,
            'monthly_breakdown' => $monthlyCosts,
            'period_months' => $period,
            'cost_per_sqft' => $this->calculateCostPerSqft($propertyId, $totalCost),
        ];
    }

    /**
     * Generate comprehensive maintenance reports
     */
    public function generateMaintenanceReports($propertyId): array
    {
        $property = Property::findOrFail($propertyId);
        
        // Get maintenance data for the last 12 months
        $maintenanceData = $this->trackMaintenanceCosts($propertyId, 12);
        
        // Get vendor performance
        $vendorPerformance = $this->getVendorPerformance($propertyId);
        
        // Get maintenance trends
        $maintenanceTrends = $this->getMaintenanceTrends($propertyId);
        
        // Get upcoming maintenance
        $upcomingMaintenance = $this->getUpcomingMaintenance($propertyId);
        
        // Get maintenance efficiency metrics
        $efficiencyMetrics = $this->getMaintenanceEfficiencyMetrics($propertyId);

        return [
            'property_id' => $propertyId,
            'property_name' => $property->name,
            'report_date' => Carbon::now()->toDateString(),
            'maintenance_data' => $maintenanceData,
            'vendor_performance' => $vendorPerformance,
            'maintenance_trends' => $maintenanceTrends,
            'upcoming_maintenance' => $upcomingMaintenance,
            'efficiency_metrics' => $efficiencyMetrics,
            'recommendations' => $this->generateMaintenanceRecommendations($maintenanceData, $vendorPerformance),
        ];
    }

    /**
     * Get vendor performance metrics
     */
    public function getVendorPerformance($propertyId): array
    {
        $vendors = Vendor::whereHas('maintenanceRequests', function($query) use ($propertyId) {
            $query->where('property_id', $propertyId);
        })->with(['maintenanceRequests' => function($query) use ($propertyId) {
            $query->where('property_id', $propertyId);
        }])->get();

        return $vendors->map(function($vendor) {
            $requests = $vendor->maintenanceRequests;
            $completedRequests = $requests->where('status', 'completed');
            
            return [
                'vendor_id' => $vendor->id,
                'vendor_name' => $vendor->name,
                'total_requests' => $requests->count(),
                'completed_requests' => $completedRequests->count(),
                'completion_rate' => $requests->count() > 0 ? 
                    round(($completedRequests->count() / $requests->count()) * 100, 2) : 0,
                'average_completion_time' => $this->calculateAverageCompletionTime($completedRequests),
                'total_cost' => $requests->sum('cost'),
                'average_cost' => $requests->avg('cost'),
                'rating' => $vendor->rating ?? 0,
            ];
        })->toArray();
    }

    /**
     * Get maintenance trends
     */
    public function getMaintenanceTrends($propertyId): array
    {
        $trends = [];
        
        // Trend over last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $monthlyData = MaintenanceRequest::where('property_id', $propertyId)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->get();

            $trends[] = [
                'month' => $monthStart->format('Y-m'),
                'month_name' => $monthStart->format('M Y'),
                'request_count' => $monthlyData->count(),
                'total_cost' => $monthlyData->sum('cost'),
                'average_cost' => $monthlyData->avg('cost'),
                'emergency_requests' => $monthlyData->where('priority', 'urgent')->count(),
                'preventive_requests' => $monthlyData->where('type', 'preventive')->count(),
            ];
        }

        return $trends;
    }

    /**
     * Get upcoming maintenance
     */
    public function getUpcomingMaintenance($propertyId): array
    {
        $upcoming = MaintenanceRequest::where('property_id', $propertyId)
            ->where('scheduled_date', '>=', Carbon::now())
            ->where('status', '!=', 'completed')
            ->orderBy('scheduled_date')
            ->with('vendor')
            ->get();

        return $upcoming->map(function($request) {
            return [
                'id' => $request->id,
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'priority' => $request->priority,
                'type' => $request->type,
                'scheduled_date' => $request->scheduled_date,
                'estimated_cost' => $request->estimated_cost,
                'vendor_name' => $request->vendor->name ?? 'Not Assigned',
                'days_until_due' => Carbon::now()->diffInDays($request->scheduled_date, false),
            ];
        })->toArray();
    }

    /**
     * Get maintenance efficiency metrics
     */
    public function getMaintenanceEfficiencyMetrics($propertyId): array
    {
        $requests = MaintenanceRequest::where('property_id', $propertyId)
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->get();

        $completedRequests = $requests->where('status', 'completed');
        $emergencyRequests = $requests->where('priority', 'urgent');
        $preventiveRequests = $requests->where('type', 'preventive');

        return [
            'total_requests' => $requests->count(),
            'completed_requests' => $completedRequests->count(),
            'completion_rate' => $requests->count() > 0 ? 
                round(($completedRequests->count() / $requests->count()) * 100, 2) : 0,
            'average_completion_time' => $this->calculateAverageCompletionTime($completedRequests),
            'emergency_request_rate' => $requests->count() > 0 ? 
                round(($emergencyRequests->count() / $requests->count()) * 100, 2) : 0,
            'preventive_maintenance_rate' => $requests->count() > 0 ? 
                round(($preventiveRequests->count() / $requests->count()) * 100, 2) : 0,
            'cost_efficiency_score' => $this->calculateCostEfficiencyScore($requests),
        ];
    }

    /**
     * Calculate next due date based on frequency
     */
    private function calculateNextDueDate($frequency): Carbon
    {
        return match($frequency) {
            'daily' => Carbon::now()->addDay(),
            'weekly' => Carbon::now()->addWeek(),
            'monthly' => Carbon::now()->addMonth(),
            'quarterly' => Carbon::now()->addMonths(3),
            'annually' => Carbon::now()->addYear(),
            default => Carbon::now()->addMonth(),
        };
    }

    /**
     * Estimate preventive maintenance cost
     */
    private function estimatePreventiveCost($category): float
    {
        return match($category) {
            'hvac' => 150.00,
            'plumbing' => 75.00,
            'electrical' => 200.00,
            'pest_control' => 100.00,
            'safety' => 50.00,
            default => 100.00,
        };
    }

    /**
     * Calculate cost per square foot
     */
    private function calculateCostPerSqft($propertyId, $totalCost): float
    {
        $property = Property::find($propertyId);
        $propertySize = $property->property_size ?? 1;
        
        return $propertySize > 0 ? round($totalCost / $propertySize, 2) : 0;
    }

    /**
     * Calculate average completion time
     */
    private function calculateAverageCompletionTime($completedRequests): float
    {
        if ($completedRequests->count() === 0) {
            return 0;
        }

        $totalDays = $completedRequests->sum(function($request) {
            return $request->created_at->diffInDays($request->completed_at ?? $request->updated_at);
        });

        return round($totalDays / $completedRequests->count(), 1);
    }

    /**
     * Calculate cost efficiency score
     */
    private function calculateCostEfficiencyScore($requests): int
    {
        $totalCost = $requests->sum('cost');
        $preventiveCost = $requests->where('type', 'preventive')->sum('cost');
        $emergencyCost = $requests->where('priority', 'urgent')->sum('cost');

        if ($totalCost === 0) {
            return 100;
        }

        $preventiveRatio = ($preventiveCost / $totalCost) * 100;
        $emergencyRatio = ($emergencyCost / $totalCost) * 100;

        // Higher score for more preventive maintenance and fewer emergencies
        $score = $preventiveRatio - ($emergencyRatio * 0.5);
        
        return max(0, min(100, round($score)));
    }

    /**
     * Generate maintenance recommendations
     */
    private function generateMaintenanceRecommendations($maintenanceData, $vendorPerformance): array
    {
        $recommendations = [];

        // Cost analysis recommendations
        if ($maintenanceData['cost_per_sqft'] > 5) {
            $recommendations[] = [
                'type' => 'cost_optimization',
                'priority' => 'high',
                'title' => 'High Maintenance Costs',
                'description' => 'Maintenance costs per square foot are above average. Consider preventive maintenance to reduce emergency repairs.',
            ];
        }

        // Vendor performance recommendations
        $lowPerformingVendors = collect($vendorPerformance)->where('completion_rate', '<', 80);
        if ($lowPerformingVendors->count() > 0) {
            $recommendations[] = [
                'type' => 'vendor_management',
                'priority' => 'medium',
                'title' => 'Vendor Performance Issues',
                'description' => 'Some vendors have low completion rates. Consider reviewing vendor contracts or finding alternatives.',
            ];
        }

        // Preventive maintenance recommendations
        $preventiveRatio = $maintenanceData['costs_by_category']['preventive']['percentage'] ?? 0;
        if ($preventiveRatio < 30) {
            $recommendations[] = [
                'type' => 'preventive_maintenance',
                'priority' => 'medium',
                'title' => 'Increase Preventive Maintenance',
                'description' => 'Low percentage of preventive maintenance. Increase scheduled maintenance to reduce emergency repairs.',
            ];
        }

        return $recommendations;
    }
}
