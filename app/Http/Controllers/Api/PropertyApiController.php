<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyRequest;
use App\Models\Property;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PropertyApiController extends Controller
{
    use ApiResponse;

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
}
