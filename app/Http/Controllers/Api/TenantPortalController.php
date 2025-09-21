<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TenantPortalService;
use App\Services\SecurityService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TenantPortalController extends Controller
{
    use ApiResponse;

    protected $tenantPortalService;
    protected $securityService;

    public function __construct(TenantPortalService $tenantPortalService, SecurityService $securityService)
    {
        $this->tenantPortalService = $tenantPortalService;
        $this->securityService = $securityService;
    }

    /**
     * Get tenant dashboard data
     */
    public function dashboard(Request $request, $tenantId): JsonResponse
    {
        $this->authorize('view tenant');
        $this->securityService->auditPropertyAccess($tenantId, auth()->id(), 'tenant_dashboard_view');

        try {
            $dashboardData = $this->tenantPortalService->getTenantDashboard($tenantId);
            return $this->successResponse($dashboardData, 'Tenant dashboard data retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve tenant dashboard: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get tenant leases
     */
    public function leases(Request $request, $tenantId): JsonResponse
    {
        $this->authorize('view tenant');
        
        try {
            $leases = $this->tenantPortalService->getActiveLeases($tenantId);
            return $this->successResponse($leases, 'Tenant leases retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve tenant leases: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get tenant payment history
     */
    public function payments(Request $request, $tenantId): JsonResponse
    {
        $this->authorize('view tenant');
        
        $limit = $request->get('limit', 10);
        
        try {
            $payments = $this->tenantPortalService->getPaymentHistory($tenantId, $limit);
            return $this->successResponse($payments, 'Tenant payment history retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve payment history: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get tenant maintenance requests
     */
    public function maintenanceRequests(Request $request, $tenantId): JsonResponse
    {
        $this->authorize('view tenant');
        
        $limit = $request->get('limit', 10);
        
        try {
            $requests = $this->tenantPortalService->getMaintenanceRequests($tenantId, $limit);
            return $this->successResponse($requests, 'Maintenance requests retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve maintenance requests: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Submit maintenance request
     */
    public function submitMaintenanceRequest(Request $request, $tenantId): JsonResponse
    {
        $this->authorize('create maintenance request');
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'category' => 'nullable|string|max:100',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
        ]);

        try {
            $result = $this->tenantPortalService->submitMaintenanceRequest($tenantId, $validated);
            
            if ($result['success']) {
                return $this->successResponse($result, 'Maintenance request submitted successfully');
            } else {
                return $this->errorResponse($result['message'], 400);
            }
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to submit maintenance request: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get tenant documents
     */
    public function documents(Request $request, $tenantId): JsonResponse
    {
        $this->authorize('view tenant');
        
        try {
            $documents = $this->tenantPortalService->getTenantDocuments($tenantId);
            return $this->successResponse($documents, 'Tenant documents retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve tenant documents: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get tenant notifications
     */
    public function notifications(Request $request, $tenantId): JsonResponse
    {
        $this->authorize('view tenant');
        
        $limit = $request->get('limit', 10);
        
        try {
            $notifications = $this->tenantPortalService->getNotifications($tenantId, $limit);
            return $this->successResponse($notifications, 'Tenant notifications retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve notifications: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get tenant financial summary
     */
    public function financialSummary(Request $request, $tenantId): JsonResponse
    {
        $this->authorize('view tenant');
        
        try {
            $summary = $this->tenantPortalService->getFinancialSummary($tenantId);
            return $this->successResponse($summary, 'Financial summary retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve financial summary: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update tenant profile
     */
    public function updateProfile(Request $request, $tenantId): JsonResponse
    {
        $this->authorize('edit tenant');
        
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|max:2048',
        ]);

        try {
            $result = $this->tenantPortalService->updateTenantProfile($tenantId, $validated);
            
            if ($result['success']) {
                return $this->successResponse($result, 'Tenant profile updated successfully');
            } else {
                return $this->errorResponse($result['message'], 400);
            }
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update tenant profile: ' . $e->getMessage(), 500);
        }
    }
}
