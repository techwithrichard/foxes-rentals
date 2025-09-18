<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Models\PropertyConsolidated;
use App\Models\PropertyDetail;
use Illuminate\Http\Request;

class PropertyConsolidatedController extends Controller
{
    /**
     * Display a listing of consolidated properties.
     */
    public function index(Request $request)
    {
        $query = PropertyConsolidated::with(['propertyType', 'landlord', 'address', 'details']);

        // Filter by subtype
        if ($request->filled('subtype')) {
            $query->where('property_subtype', $request->subtype);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by availability
        if ($request->filled('available')) {
            $query->where('is_available', $request->boolean('available'));
        }

        // Filter by vacancy
        if ($request->filled('vacant')) {
            $query->where('is_vacant', $request->boolean('vacant'));
        }

        // Search by name or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $properties = $query->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $properties,
            'message' => 'Consolidated properties retrieved successfully'
        ]);
    }

    /**
     * Display the specified consolidated property.
     */
    public function show(PropertyConsolidated $propertyConsolidated)
    {
        $propertyConsolidated->load(['propertyType', 'landlord', 'address', 'details', 'leases', 'inquiries', 'applications']);

        return response()->json([
            'success' => true,
            'data' => $propertyConsolidated,
            'message' => 'Property retrieved successfully'
        ]);
    }

    /**
     * Create a new consolidated property.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'property_type_id' => 'required|exists:property_types,id',
            'landlord_id' => 'nullable|exists:users,id',
            'property_subtype' => 'required|in:rental,sale,lease',
            'base_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'nullable|in:active,inactive,maintenance,sold',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'images' => 'nullable|array',
        ]);

        $property = PropertyConsolidated::create($request->all());

        // Create property details if subtype-specific data is provided
        if ($request->filled('detail_data')) {
            PropertyDetail::create([
                'property_id' => $property->id,
                'detail_type' => $property->property_subtype,
                'detail_data' => $request->detail_data,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $property->load(['propertyType', 'landlord', 'details']),
            'message' => 'Property created successfully'
        ], 201);
    }

    /**
     * Update the specified consolidated property.
     */
    public function update(Request $request, PropertyConsolidated $propertyConsolidated)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'property_type_id' => 'sometimes|exists:property_types,id',
            'landlord_id' => 'nullable|exists:users,id',
            'base_amount' => 'sometimes|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'nullable|in:active,inactive,maintenance,sold',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'images' => 'nullable|array',
        ]);

        $propertyConsolidated->update($request->all());

        // Update property details if provided
        if ($request->filled('detail_data')) {
            $propertyConsolidated->details()
                ->where('detail_type', $propertyConsolidated->property_subtype)
                ->update(['detail_data' => $request->detail_data]);
        }

        return response()->json([
            'success' => true,
            'data' => $propertyConsolidated->load(['propertyType', 'landlord', 'details']),
            'message' => 'Property updated successfully'
        ]);
    }

    /**
     * Get property statistics.
     */
    public function statistics()
    {
        $stats = [
            'total_properties' => PropertyConsolidated::count(),
            'rental_properties' => PropertyConsolidated::rental()->count(),
            'sale_properties' => PropertyConsolidated::sale()->count(),
            'lease_properties' => PropertyConsolidated::lease()->count(),
            'active_properties' => PropertyConsolidated::active()->count(),
            'vacant_properties' => PropertyConsolidated::vacant()->count(),
            'available_properties' => PropertyConsolidated::available()->count(),
            'featured_properties' => PropertyConsolidated::featured()->count(),
            'published_properties' => PropertyConsolidated::published()->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Statistics retrieved successfully'
        ]);
    }

    /**
     * Test property relationships.
     */
    public function testRelationships(PropertyConsolidated $propertyConsolidated)
    {
        $relationships = [
            'property_type' => $propertyConsolidated->propertyType,
            'landlord' => $propertyConsolidated->landlord,
            'address' => $propertyConsolidated->address,
            'details' => $propertyConsolidated->details,
            'rental_details' => $propertyConsolidated->rentalDetails,
            'sale_details' => $propertyConsolidated->saleDetails,
            'lease_details' => $propertyConsolidated->leaseDetails,
            'leases' => $propertyConsolidated->leases,
            'active_leases' => $propertyConsolidated->activeLeases,
            'inquiries' => $propertyConsolidated->inquiries,
            'applications' => $propertyConsolidated->applications,
            'maintenance_requests' => $propertyConsolidated->maintenanceRequests,
        ];

        return response()->json([
            'success' => true,
            'data' => $relationships,
            'message' => 'Relationships tested successfully'
        ]);
    }
}
