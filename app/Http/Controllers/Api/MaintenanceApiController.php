<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MaintenanceManagementService;
use App\Services\SecurityService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MaintenanceApiController extends Controller
{
    use ApiResponse;

    protected $maintenanceService;
    protected $securityService;

    public function __construct(MaintenanceManagementService $maintenanceService, SecurityService $securityService)
    {
        $this->maintenanceService = $maintenanceService;
        $this->securityService = $securityService;
    }

    /**
     * Schedule preventive maintenance for property
     */
    public function schedulePreventiveMaintenance(Request $request, $propertyId): JsonResponse
    {
        $this->authorize('manage maintenance');
        $this->securityService->auditPropertyAccess($propertyId, auth()->id(), 'preventive_maintenance_schedule');

        try {
            $result = $this->maintenanceService->schedulePreventiveMaintenance($propertyId);
            return $this->successResponse($result, 'Preventive maintenance scheduled successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to schedule preventive maintenance: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Assign vendor to maintenance request
     */
    public function assignVendor(Request $request, $requestId): JsonResponse
    {
        $this->authorize('manage maintenance');
        
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
        ]);

        try {
            $result = $this->maintenanceService->assignVendor($requestId, $validated['vendor_id']);
            return $this->successResponse($result, 'Vendor assigned successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to assign vendor: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get maintenance costs for property
     */
    public function getMaintenanceCosts(Request $request, $propertyId): JsonResponse
    {
        $this->authorize('view maintenance');
        $this->securityService->auditPropertyAccess($propertyId, auth()->id(), 'maintenance_costs_view');

        $period = $request->get('period', 12);

        try {
            $costs = $this->maintenanceService->trackMaintenanceCosts($propertyId, $period);
            return $this->successResponse($costs, 'Maintenance costs retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve maintenance costs: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Generate maintenance reports
     */
    public function generateReports($propertyId): JsonResponse
    {
        $this->authorize('view maintenance');
        $this->securityService->auditPropertyAccess($propertyId, auth()->id(), 'maintenance_reports_view');

        try {
            $reports = $this->maintenanceService->generateMaintenanceReports($propertyId);
            return $this->successResponse($reports, 'Maintenance reports generated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate maintenance reports: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get vendor performance metrics
     */
    public function getVendorPerformance(Request $request, $propertyId): JsonResponse
    {
        $this->authorize('view maintenance');

        try {
            $performance = $this->maintenanceService->getVendorPerformance($propertyId);
            return $this->successResponse($performance, 'Vendor performance retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve vendor performance: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get maintenance trends
     */
    public function getMaintenanceTrends(Request $request, $propertyId): JsonResponse
    {
        $this->authorize('view maintenance');

        try {
            $trends = $this->maintenanceService->getMaintenanceTrends($propertyId);
            return $this->successResponse($trends, 'Maintenance trends retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve maintenance trends: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get upcoming maintenance
     */
    public function getUpcomingMaintenance(Request $request, $propertyId): JsonResponse
    {
        $this->authorize('view maintenance');

        try {
            $upcoming = $this->maintenanceService->getUpcomingMaintenance($propertyId);
            return $this->successResponse($upcoming, 'Upcoming maintenance retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve upcoming maintenance: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get maintenance efficiency metrics
     */
    public function getEfficiencyMetrics(Request $request, $propertyId): JsonResponse
    {
        $this->authorize('view maintenance');

        try {
            $metrics = $this->maintenanceService->getMaintenanceEfficiencyMetrics($propertyId);
            return $this->successResponse($metrics, 'Efficiency metrics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve efficiency metrics: ' . $e->getMessage(), 500);
        }
    }
}
