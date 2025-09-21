<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyRequest;
use App\Models\Property;
use App\Models\RentalProperty;
use App\Models\SaleProperty;
use App\Models\LeaseProperty;
use App\Services\FinancialAnalyticsService;
use App\Services\SecurityService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PropertyApiController extends Controller
{
    use ApiResponse;

    protected $financialAnalytics;
    protected $securityService;

    public function __construct(FinancialAnalyticsService $financialAnalytics, SecurityService $securityService)
    {
        $this->financialAnalytics = $financialAnalytics;
        $this->securityService = $securityService;
    }

    /**
     * Get all properties with filtering and pagination
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('view property');

        $query = Property::with(['landlord:id,name', 'address', 'lease.tenant:id,name'])
            ->withCount('houses');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('landlord_id')) {
            $query->where('landlord_id', $request->get('landlord_id'));
        }

        if ($request->filled('is_vacant')) {
            $query->where('is_vacant', $request->get('is_vacant'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $properties = $query->latest()->paginate($perPage);

        return $this->paginatedResponse($properties, 'Properties retrieved successfully');
    }

    /**
     * Get property by ID
     */
    public function show(string $id): JsonResponse
    {
        $this->authorize('view property');

        $property = Property::with(['landlord', 'address', 'houses', 'leases.tenant'])
            ->find($id);

        if (!$property) {
            return $this->notFoundResponse('Property not found');
        }

        return $this->successResponse($property, 'Property retrieved successfully');
    }

    /**
     * Create new property
     */
    public function store(PropertyRequest $request): JsonResponse
    {
        $this->authorize('create property');

        try {
            DB::beginTransaction();

            $validated = $request->validated();

            // Create the property
            $property = Property::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'type' => $validated['type'],
                'rent' => $validated['rent'],
                'deposit' => $validated['deposit'],
                'landlord_id' => $validated['landlord_id'],
                'commission' => $validated['commission'] ?? 0,
                'status' => $validated['status'],
                'is_vacant' => $validated['is_vacant'] ?? true,
                'electricity_id' => $validated['electricity_id'],
            ]);

            // Create address if provided
            if (isset($validated['address']) && !empty(array_filter($validated['address']))) {
                $property->address()->create([
                    'street' => $validated['address']['street'] ?? null,
                    'city' => $validated['address']['city'] ?? null,
                    'state' => $validated['address']['state'] ?? null,
                    'postal_code' => $validated['address']['postal_code'] ?? null,
                    'country' => $validated['address']['country'] ?? null,
                ]);
            }

            DB::commit();

            return $this->createdResponse($property->load(['landlord', 'address']), 'Property created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverErrorResponse('Failed to create property', $e->getMessage());
        }
    }

    /**
     * Update property
     */
    public function update(PropertyRequest $request, string $id): JsonResponse
    {
        $this->authorize('edit property');

        $property = Property::find($id);

        if (!$property) {
            return $this->notFoundResponse('Property not found');
        }

        try {
            DB::beginTransaction();

            $validated = $request->validated();

            // Update the property
            $property->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'type' => $validated['type'],
                'rent' => $validated['rent'],
                'deposit' => $validated['deposit'],
                'landlord_id' => $validated['landlord_id'],
                'commission' => $validated['commission'] ?? 0,
                'status' => $validated['status'],
                'is_vacant' => $validated['is_vacant'] ?? true,
                'electricity_id' => $validated['electricity_id'],
            ]);

            // Update or create address
            if (isset($validated['address']) && !empty(array_filter($validated['address']))) {
                if ($property->address) {
                    $property->address->update([
                        'street' => $validated['address']['street'] ?? null,
                        'city' => $validated['address']['city'] ?? null,
                        'state' => $validated['address']['state'] ?? null,
                        'postal_code' => $validated['address']['postal_code'] ?? null,
                        'country' => $validated['address']['country'] ?? null,
                    ]);
                } else {
                    $property->address()->create([
                        'street' => $validated['address']['street'] ?? null,
                        'city' => $validated['address']['city'] ?? null,
                        'state' => $validated['address']['state'] ?? null,
                        'postal_code' => $validated['address']['postal_code'] ?? null,
                        'country' => $validated['address']['country'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return $this->updatedResponse($property->load(['landlord', 'address']), 'Property updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverErrorResponse('Failed to update property', $e->getMessage());
        }
    }

    /**
     * Delete property
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorize('delete property');

        $property = Property::find($id);

        if (!$property) {
            return $this->notFoundResponse('Property not found');
        }

        try {
            DB::beginTransaction();

            // Soft delete related records first
            $property->leases()->delete();
            $property->houses()->delete();
            
            // Then soft delete the property itself
            $property->delete();

            DB::commit();

            return $this->deletedResponse('Property deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverErrorResponse('Failed to delete property', $e->getMessage());
        }
    }

    /**
     * Get property statistics
     */
    public function statistics(): JsonResponse
    {
        $this->authorize('view property');

        $stats = [
            'total_properties' => Property::count(),
            'active_properties' => Property::where('status', 'active')->count(),
            'vacant_properties' => Property::where('is_vacant', true)->count(),
            'occupied_properties' => Property::where('is_vacant', false)->count(),
            'properties_by_type' => Property::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type'),
            'properties_by_status' => Property::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
        ];

        return $this->successResponse($stats, 'Property statistics retrieved successfully');
    }

    /**
     * Get property financial analytics
     */
    public function financialAnalytics(Request $request, $propertyId): JsonResponse
    {
        $this->authorize('view property');
        $this->securityService->auditPropertyAccess($propertyId, auth()->id(), 'financial_analytics_view');

        $period = $request->get('period', 12);
        
        try {
            $analytics = $this->financialAnalytics->generatePropertyFinancialReport($propertyId, $period);
            return $this->successResponse($analytics, 'Financial analytics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve financial analytics: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get property ROI analysis
     */
    public function roiAnalysis(Request $request, $propertyId): JsonResponse
    {
        $this->authorize('view property');
        $this->securityService->auditPropertyAccess($propertyId, auth()->id(), 'roi_analysis_view');

        $period = $request->get('period', 12);
        
        try {
            $roi = $this->financialAnalytics->getPropertyROI($propertyId, $period);
            return $this->successResponse($roi, 'ROI analysis retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve ROI analysis: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get property occupancy analytics
     */
    public function occupancyAnalytics(Request $request, $propertyId): JsonResponse
    {
        $this->authorize('view property');
        $this->securityService->auditPropertyAccess($propertyId, auth()->id(), 'occupancy_analytics_view');

        $period = $request->get('period', 12);
        
        try {
            $occupancy = $this->financialAnalytics->getOccupancyRevenue($propertyId, $period);
            return $this->successResponse($occupancy, 'Occupancy analytics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve occupancy analytics: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get property maintenance costs
     */
    public function maintenanceCosts(Request $request, $propertyId): JsonResponse
    {
        $this->authorize('view property');
        $this->securityService->auditPropertyAccess($propertyId, auth()->id(), 'maintenance_costs_view');

        $period = $request->get('period', 12);
        
        try {
            $maintenance = $this->financialAnalytics->getMaintenanceCosts($propertyId, $period);
            return $this->successResponse($maintenance, 'Maintenance costs retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve maintenance costs: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get market comparison for property
     */
    public function marketComparison($propertyId): JsonResponse
    {
        $this->authorize('view property');
        $this->securityService->auditPropertyAccess($propertyId, auth()->id(), 'market_comparison_view');
        
        try {
            $comparison = $this->financialAnalytics->getMarketComparison($propertyId);
            return $this->successResponse($comparison, 'Market comparison retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve market comparison: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get portfolio analytics
     */
    public function portfolioAnalytics(Request $request): JsonResponse
    {
        $this->authorize('view property');

        $landlordId = $request->get('landlord_id');
        $period = $request->get('period', 12);
        
        try {
            $analytics = $this->financialAnalytics->getPortfolioAnalytics($landlordId, $period);
            return $this->successResponse($analytics, 'Portfolio analytics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve portfolio analytics: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get revenue trends for property
     */
    public function revenueTrends(Request $request, $propertyId): JsonResponse
    {
        $this->authorize('view property');
        $this->securityService->auditPropertyAccess($propertyId, auth()->id(), 'revenue_trends_view');

        $period = $request->get('period', 12);
        
        try {
            $trends = $this->financialAnalytics->getRevenueTrends($propertyId, $period);
            return $this->successResponse($trends, 'Revenue trends retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve revenue trends: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get comprehensive property dashboard data
     */
    public function dashboardData($propertyId): JsonResponse
    {
        $this->authorize('view property');
        $this->securityService->auditPropertyAccess($propertyId, auth()->id(), 'dashboard_data_view');
        
        try {
            $property = Property::with(['address', 'landlord', 'propertyType'])->findOrFail($propertyId);
            
            $dashboardData = [
                'property' => $property,
                'financial_summary' => $this->financialAnalytics->generatePropertyFinancialReport($propertyId, 12),
                'recent_activity' => $this->getRecentActivity($propertyId),
                'upcoming_events' => $this->getUpcomingEvents($propertyId),
                'maintenance_status' => $this->getMaintenanceStatus($propertyId),
            ];

            return $this->successResponse($dashboardData, 'Dashboard data retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve dashboard data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get recent activity for property
     */
    private function getRecentActivity($propertyId): array
    {
        // This would integrate with activity logs
        return [
            'recent_leases' => Lease::where('property_id', $propertyId)
                ->with('tenant')
                ->latest()
                ->limit(5)
                ->get(),
            'recent_payments' => Payment::whereHas('invoice.lease', function($query) use ($propertyId) {
                $query->where('property_id', $propertyId);
            })
            ->latest()
            ->limit(5)
            ->get(),
        ];
    }

    /**
     * Get upcoming events for property
     */
    private function getUpcomingEvents($propertyId): array
    {
        return [
            'upcoming_lease_renewals' => Lease::where('property_id', $propertyId)
                ->where('end_date', '<=', now()->addDays(30))
                ->where('status', 'active')
                ->get(),
            'scheduled_maintenance' => MaintenanceRequest::where('property_id', $propertyId)
                ->where('scheduled_date', '>=', now())
                ->where('status', 'scheduled')
                ->get(),
        ];
    }

    /**
     * Get maintenance status for property
     */
    private function getMaintenanceStatus($propertyId): array
    {
        $totalRequests = MaintenanceRequest::where('property_id', $propertyId)->count();
        $pendingRequests = MaintenanceRequest::where('property_id', $propertyId)
            ->where('status', 'pending')
            ->count();
        $completedRequests = MaintenanceRequest::where('property_id', $propertyId)
            ->where('status', 'completed')
            ->count();

        return [
            'total_requests' => $totalRequests,
            'pending_requests' => $pendingRequests,
            'completed_requests' => $completedRequests,
            'completion_rate' => $totalRequests > 0 ? ($completedRequests / $totalRequests) * 100 : 0,
        ];
    }
}
