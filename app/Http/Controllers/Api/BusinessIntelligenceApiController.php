<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BusinessIntelligenceService;
use App\Services\SecurityService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BusinessIntelligenceApiController extends Controller
{
    use ApiResponse;

    protected $biService;
    protected $securityService;

    public function __construct(BusinessIntelligenceService $biService, SecurityService $securityService)
    {
        $this->biService = $biService;
        $this->securityService = $securityService;
    }

    /**
     * Get comprehensive business intelligence dashboard
     */
    public function getDashboardData(Request $request): JsonResponse
    {
        $this->authorize('view analytics');

        $landlordId = $request->get('landlord_id');
        $period = $request->get('period', 12);

        try {
            $dashboardData = $this->biService->generateDashboardData($landlordId, $period);
            return $this->successResponse($dashboardData, 'Business intelligence dashboard data retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve dashboard data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get overview metrics
     */
    public function getOverviewMetrics(Request $request): JsonResponse
    {
        $this->authorize('view analytics');

        $landlordId = $request->get('landlord_id');
        $period = $request->get('period', 12);

        try {
            $metrics = $this->biService->generateDashboardData($landlordId, $period);
            return $this->successResponse($metrics['overview'], 'Overview metrics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve overview metrics: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get financial performance metrics
     */
    public function getFinancialPerformance(Request $request): JsonResponse
    {
        $this->authorize('view analytics');

        $landlordId = $request->get('landlord_id');
        $period = $request->get('period', 12);

        try {
            $performance = $this->biService->generateDashboardData($landlordId, $period);
            return $this->successResponse($performance['financial_performance'], 'Financial performance retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve financial performance: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get property performance metrics
     */
    public function getPropertyPerformance(Request $request): JsonResponse
    {
        $this->authorize('view analytics');

        $landlordId = $request->get('landlord_id');
        $period = $request->get('period', 12);

        try {
            $performance = $this->biService->generateDashboardData($landlordId, $period);
            return $this->successResponse($performance['property_performance'], 'Property performance retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve property performance: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get tenant analytics
     */
    public function getTenantAnalytics(Request $request): JsonResponse
    {
        $this->authorize('view analytics');

        $landlordId = $request->get('landlord_id');
        $period = $request->get('period', 12);

        try {
            $analytics = $this->biService->generateDashboardData($landlordId, $period);
            return $this->successResponse($analytics['tenant_analytics'], 'Tenant analytics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve tenant analytics: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get maintenance insights
     */
    public function getMaintenanceInsights(Request $request): JsonResponse
    {
        $this->authorize('view analytics');

        $landlordId = $request->get('landlord_id');
        $period = $request->get('period', 12);

        try {
            $insights = $this->biService->generateDashboardData($landlordId, $period);
            return $this->successResponse($insights['maintenance_insights'], 'Maintenance insights retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve maintenance insights: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get market analysis
     */
    public function getMarketAnalysis(Request $request): JsonResponse
    {
        $this->authorize('view analytics');

        $landlordId = $request->get('landlord_id');

        try {
            $analysis = $this->biService->generateDashboardData($landlordId, 12);
            return $this->successResponse($analysis['market_analysis'], 'Market analysis retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve market analysis: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get predictive analytics
     */
    public function getPredictiveAnalytics(Request $request): JsonResponse
    {
        $this->authorize('view analytics');

        $landlordId = $request->get('landlord_id');
        $period = $request->get('period', 12);

        try {
            $predictions = $this->biService->generateDashboardData($landlordId, $period);
            return $this->successResponse($predictions['predictive_analytics'], 'Predictive analytics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve predictive analytics: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get business recommendations
     */
    public function getRecommendations(Request $request): JsonResponse
    {
        $this->authorize('view analytics');

        $landlordId = $request->get('landlord_id');
        $period = $request->get('period', 12);

        try {
            $recommendations = $this->biService->generateDashboardData($landlordId, $period);
            return $this->successResponse($recommendations['recommendations'], 'Business recommendations retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve recommendations: ' . $e->getMessage(), 500);
        }
    }
}
