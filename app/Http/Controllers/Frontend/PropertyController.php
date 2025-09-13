<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\House;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with(['address', 'landlord', 'houses'])
            ->where('status', 'active')
            ->where('is_vacant', true);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by rent range
        if ($request->filled('min_rent')) {
            $query->where('rent', '>=', $request->get('min_rent'));
        }
        if ($request->filled('max_rent')) {
            $query->where('rent', '<=', $request->get('max_rent'));
        }

        // Filter by property type
        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        // Filter by location
        if ($request->filled('location')) {
            $query->whereHas('address', function ($q) use ($request) {
                $q->where('city', 'like', "%{$request->get('location')}%")
                  ->orWhere('state', 'like', "%{$request->get('location')}%");
            });
        }

        $properties = $query->paginate(12);

        return view('frontend.properties.index', compact('properties'));
    }

    public function show($id)
    {
        $property = Property::with(['address', 'landlord', 'houses', 'leases.tenant'])
            ->findOrFail($id);

        // Get related properties
        $relatedProperties = Property::with(['address'])
            ->where('id', '!=', $id)
            ->where('status', 'active')
            ->where('is_vacant', true)
            ->limit(4)
            ->get();

        return view('frontend.properties.show', compact('property', 'relatedProperties'));
    }

    public function search(Request $request)
    {
        return $this->index($request);
    }
}
