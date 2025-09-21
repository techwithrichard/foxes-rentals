<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WorkflowAutomationService;
use App\Services\SecurityService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WorkflowApiController extends Controller
{
    use ApiResponse;

    protected $workflowService;
    protected $securityService;

    public function __construct(WorkflowAutomationService $workflowService, SecurityService $securityService)
    {
        $this->workflowService = $workflowService;
        $this->securityService = $securityService;
    }

    /**
     * Process lease renewal automation
     */
    public function processLeaseRenewal(Request $request, $leaseId): JsonResponse
    {
        $this->authorize('manage leases');
        $this->securityService->auditPropertyAccess($leaseId, auth()->id(), 'lease_renewal_process');

        try {
            $result = $this->workflowService->processLeaseRenewal($leaseId);
            return $this->successResponse($result, 'Lease renewal processed successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to process lease renewal: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Handle payment reminder automation
     */
    public function handlePaymentReminders(): JsonResponse
    {
        $this->authorize('manage payments');

        try {
            $result = $this->workflowService->handlePaymentReminders();
            return $this->successResponse($result, 'Payment reminders processed successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to process payment reminders: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Process maintenance request automation
     */
    public function processMaintenanceRequests(): JsonResponse
    {
        $this->authorize('manage maintenance');

        try {
            $result = $this->workflowService->processMaintenanceRequests();
            return $this->successResponse($result, 'Maintenance requests processed successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to process maintenance requests: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Generate monthly reports automation
     */
    public function generateMonthlyReports(Request $request): JsonResponse
    {
        $this->authorize('view reports');

        $propertyId = $request->get('property_id');

        try {
            $result = $this->workflowService->generateMonthlyReports($propertyId);
            return $this->successResponse($result, 'Monthly reports generated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate monthly reports: ' . $e->getMessage(), 500);
        }
    }
}
