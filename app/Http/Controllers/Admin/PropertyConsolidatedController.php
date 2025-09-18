<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PropertyConsolidated;
use App\Models\PropertyType;
use App\Models\User;
use App\Services\PropertyService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PropertyConsolidatedController extends Controller
{
    protected $propertyService;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    /**
     * Display a listing of properties
     */
    public function index(Request $request): View
    {
        $this->authorize('view property');

        $filters = $request->only([
            'subtype', 'status', 'available', 'vacant', 'min_price', 'max_price',
            'bedrooms', 'bathrooms', 'location', 'landlord_id', 'property_type_id',
            'featured', 'published'
        ]);

        $properties = $this->propertyService->searchProperties($filters);
        $propertyTypes = PropertyType::active()->get();
        $landlords = User::role('landlord')->get();

        return view('admin.properties-consolidated.index', compact('properties', 'propertyTypes', 'landlords'));
    }

    /**
     * Show the form for creating a new property
     */
    public function create(Request $request): View
    {
        $this->authorize('create property');

        $type = $request->get('type', 'rental');
        $propertyTypes = PropertyType::active()->get();
        $landlords = User::role('landlord')->get();

        return view('admin.properties-consolidated.create', compact('type', 'propertyTypes', 'landlords'));
    }

    /**
     * Store a newly created property
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create property');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'property_type_id' => 'required|exists:property_types,id',
            'landlord_id' => 'nullable|exists:users,id',
            'type' => 'required|in:rental,sale,lease',
            'base_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'images' => 'nullable|array',
            'detail_data' => 'nullable|array',
            'address' => 'nullable|array',
        ]);

        try {
            $property = $this->propertyService->createProperty($validated, $validated['type']);

            return response()->json([
                'success' => true,
                'message' => 'Property created successfully.',
                'data' => $property
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create property: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified property
     */
    public function show(PropertyConsolidated $property): View
    {
        $this->authorize('view property');

        $property->load(['propertyType', 'landlord', 'address', 'details', 'leases', 'inquiries', 'applications', 'maintenanceRequests']);

        return view('admin.properties-consolidated.show', compact('property'));
    }

    /**
     * Show the form for editing the specified property
     */
    public function edit(PropertyConsolidated $property): View
    {
        $this->authorize('edit property');

        $property->load(['propertyType', 'landlord', 'address', 'details']);
        $propertyTypes = PropertyType::active()->get();
        $landlords = User::role('landlord')->get();

        return view('admin.properties-consolidated.edit', compact('property', 'propertyTypes', 'landlords'));
    }

    /**
     * Update the specified property
     */
    public function update(Request $request, PropertyConsolidated $property): JsonResponse
    {
        $this->authorize('edit property');

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'property_type_id' => 'sometimes|exists:property_types,id',
            'landlord_id' => 'nullable|exists:users,id',
            'base_amount' => 'sometimes|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'images' => 'nullable|array',
            'detail_data' => 'nullable|array',
            'address' => 'nullable|array',
        ]);

        try {
            $property = $this->propertyService->updateProperty($property, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Property updated successfully.',
                'data' => $property
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update property: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified property
     */
    public function destroy(PropertyConsolidated $property): JsonResponse
    {
        $this->authorize('delete property');

        try {
            $this->propertyService->deleteProperty($property);

            return response()->json([
                'success' => true,
                'message' => 'Property deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete property: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get properties by type
     */
    public function getByType(string $type): JsonResponse
    {
        $this->authorize('view property');

        try {
            $properties = $this->propertyService->getPropertiesByType($type);

            return response()->json([
                'success' => true,
                'data' => $properties
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get properties: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available properties
     */
    public function getAvailable(): JsonResponse
    {
        $this->authorize('view property');

        try {
            $properties = $this->propertyService->getAvailableProperties();

            return response()->json([
                'success' => true,
                'data' => $properties
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get available properties: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vacant properties
     */
    public function getVacant(): JsonResponse
    {
        $this->authorize('view property');

        try {
            $properties = $this->propertyService->getVacantProperties();

            return response()->json([
                'success' => true,
                'data' => $properties
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get vacant properties: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get featured properties
     */
    public function getFeatured(): JsonResponse
    {
        $this->authorize('view property');

        try {
            $properties = $this->propertyService->getFeaturedProperties();

            return response()->json([
                'success' => true,
                'data' => $properties
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get featured properties: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get property statistics
     */
    public function statistics(): JsonResponse
    {
        $this->authorize('view property');

        try {
            $statistics = $this->propertyService->getPropertyStatistics();

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle property status
     */
    public function toggleStatus(Request $request, PropertyConsolidated $property): JsonResponse
    {
        $this->authorize('edit property');

        $request->validate([
            'status' => 'required|in:active,inactive,maintenance,sold'
        ]);

        try {
            $property = $this->propertyService->togglePropertyStatus($property, $request->status);

            return response()->json([
                'success' => true,
                'message' => 'Property status updated successfully.',
                'data' => $property
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update property status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle property availability
     */
    public function toggleAvailability(PropertyConsolidated $property): JsonResponse
    {
        $this->authorize('edit property');

        try {
            $property = $this->propertyService->togglePropertyAvailability($property);

            return response()->json([
                'success' => true,
                'message' => 'Property availability updated successfully.',
                'data' => $property
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update property availability: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle property vacancy
     */
    public function toggleVacancy(PropertyConsolidated $property): JsonResponse
    {
        $this->authorize('edit property');

        try {
            $property = $this->propertyService->togglePropertyVacancy($property);

            return response()->json([
                'success' => true,
                'message' => 'Property vacancy updated successfully.',
                'data' => $property
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update property vacancy: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle property featured status
     */
    public function toggleFeatured(PropertyConsolidated $property): JsonResponse
    {
        $this->authorize('edit property');

        try {
            $property = $this->propertyService->togglePropertyFeatured($property);

            return response()->json([
                'success' => true,
                'message' => 'Property featured status updated successfully.',
                'data' => $property
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update property featured status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle property published status
     */
    public function togglePublished(PropertyConsolidated $property): JsonResponse
    {
        $this->authorize('edit property');

        try {
            $property = $this->propertyService->togglePropertyPublished($property);

            return response()->json([
                'success' => true,
                'message' => 'Property published status updated successfully.',
                'data' => $property
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update property published status: ' . $e->getMessage()
            ], 500);
        }
    }
}
