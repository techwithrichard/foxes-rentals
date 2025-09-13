<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentalProperty;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentalPropertyController extends Controller
{
    public function index()
    {
        $this->authorize('view rental property');
        
        $rentalProperties = RentalProperty::with(['propertyType', 'landlord', 'address'])
            ->withCount(['units', 'activeLeases', 'inquiries', 'applications'])
            ->latest()
            ->paginate(20);

        return view('admin.rental-properties.index', compact('rentalProperties'));
    }

    public function all()
    {
        $this->authorize('view rental property');
        
        $query = RentalProperty::with(['propertyType', 'landlord', 'address'])
            ->withCount(['units', 'activeLeases', 'inquiries', 'applications']);

        // Search functionality
        if (request()->filled('search')) {
            $search = request()->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('address', function ($addressQuery) use ($search) {
                      $addressQuery->where('city', 'like', "%{$search}%")
                                  ->orWhere('state', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if (request()->filled('status')) {
            $query->where('status', request()->get('status'));
        }

        // Filter by vacancy
        if (request()->filled('vacancy')) {
            $query->where('is_vacant', request()->get('vacancy') === 'vacant');
        }

        // Filter by property type
        if (request()->filled('property_type')) {
            $query->where('property_type_id', request()->get('property_type'));
        }

        // Filter by rent range
        if (request()->filled('min_rent')) {
            $query->where('rent_amount', '>=', request()->get('min_rent'));
        }
        if (request()->filled('max_rent')) {
            $query->where('rent_amount', '<=', request()->get('max_rent'));
        }

        // Filter by bedrooms
        if (request()->filled('bedrooms')) {
            $query->where('bedrooms', request()->get('bedrooms'));
        }

        // Filter by bathrooms
        if (request()->filled('bathrooms')) {
            $query->where('bathrooms', request()->get('bathrooms'));
        }

        // Filter by features
        if (request()->filled('features')) {
            $features = is_array(request()->get('features')) ? request()->get('features') : [request()->get('features')];
            $query->where(function ($q) use ($features) {
                foreach ($features as $feature) {
                    $q->orWhereJsonContains('features', $feature);
                }
            });
        }

        $rentalProperties = $query->latest()->paginate(20);
        $propertyTypes = PropertyType::active()->get();

        return view('admin.rental-properties.all', compact('rentalProperties', 'propertyTypes'));
    }

    public function vacant()
    {
        $this->authorize('view rental property');
        
        $rentalProperties = RentalProperty::with(['propertyType', 'landlord', 'address'])
            ->where('is_vacant', true)
            ->where('status', 'active')
            ->withCount(['units', 'inquiries', 'applications'])
            ->latest()
            ->paginate(20);

        return view('admin.rental-properties.vacant', compact('rentalProperties'));
    }

    public function occupied()
    {
        $this->authorize('view rental property');
        
        $rentalProperties = RentalProperty::with(['propertyType', 'landlord', 'address'])
            ->where('is_vacant', false)
            ->where('status', 'active')
            ->withCount(['units', 'activeLeases', 'inquiries', 'applications'])
            ->latest()
            ->paginate(20);

        return view('admin.rental-properties.occupied', compact('rentalProperties'));
    }

    public function featured()
    {
        $this->authorize('view rental property');
        
        $rentalProperties = RentalProperty::with(['propertyType', 'landlord', 'address'])
            ->where('is_featured', true)
            ->where('status', 'active')
            ->withCount(['units', 'activeLeases', 'inquiries', 'applications'])
            ->latest()
            ->paginate(20);

        return view('admin.rental-properties.featured', compact('rentalProperties'));
    }

    public function create()
    {
        $this->authorize('create rental property');
        
        $propertyTypes = PropertyType::active()->get();
        $landlords = User::role('landlord')->get();

        return view('admin.rental-properties.create', compact('propertyTypes', 'landlords'));
    }

    public function store(Request $request)
    {
        $this->authorize('create rental property');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'property_type_id' => 'required|exists:property_types,id',
            'landlord_id' => 'nullable|exists:users,id',
            'rent_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'electricity_id' => 'nullable|string|max:50',
            'water_id' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,maintenance',
            'is_multi_unit' => 'boolean',
            'total_units' => 'nullable|integer|min:1',
            'furnished' => 'boolean',
            'pet_friendly' => 'boolean',
            'smoking_allowed' => 'boolean',
            'parking_spaces' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'images' => 'nullable|array',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'property_size' => 'nullable|numeric|min:0',
            'year_built' => 'nullable|integer|min:1800|max:' . date('Y'),
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'marketing_description' => 'nullable|string|max:2000',
            'keywords' => 'nullable|string|max:500',
            // Address fields
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip' => 'nullable|string|max:20',
            'country' => 'required|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            // Create the rental property
            $rentalProperty = RentalProperty::create($validated);

            // Create address
            $rentalProperty->address()->create([
                'address1' => $validated['address1'],
                'address2' => $validated['address2'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'zip' => $validated['zip'],
                'country' => $validated['country'],
            ]);

            DB::commit();

            return redirect()->route('admin.rental-properties.show', $rentalProperty)
                ->with('success', 'Rental property created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create rental property: ' . $e->getMessage());
        }
    }

    public function show(RentalProperty $rentalProperty)
    {
        $this->authorize('view rental property');
        
        $rentalProperty->load([
            'propertyType', 'landlord', 'address', 'units', 'activeLeases.tenant',
            'inquiries', 'applications.applicant', 'maintenanceRequests'
        ]);

        return view('admin.rental-properties.show', compact('rentalProperty'));
    }

    public function edit(RentalProperty $rentalProperty)
    {
        $this->authorize('edit rental property');
        
        $propertyTypes = PropertyType::active()->get();
        $landlords = User::role('landlord')->get();
        $rentalProperty->load('address');

        return view('admin.rental-properties.edit', compact('rentalProperty', 'propertyTypes', 'landlords'));
    }

    public function update(Request $request, RentalProperty $rentalProperty)
    {
        $this->authorize('edit rental property');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'property_type_id' => 'required|exists:property_types,id',
            'landlord_id' => 'nullable|exists:users,id',
            'rent_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'electricity_id' => 'nullable|string|max:50',
            'water_id' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,maintenance',
            'is_multi_unit' => 'boolean',
            'total_units' => 'nullable|integer|min:1',
            'furnished' => 'boolean',
            'pet_friendly' => 'boolean',
            'smoking_allowed' => 'boolean',
            'parking_spaces' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'images' => 'nullable|array',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'property_size' => 'nullable|numeric|min:0',
            'year_built' => 'nullable|integer|min:1800|max:' . date('Y'),
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'marketing_description' => 'nullable|string|max:2000',
            'keywords' => 'nullable|string|max:500',
            // Address fields
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip' => 'nullable|string|max:20',
            'country' => 'required|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            // Update the rental property
            $rentalProperty->update($validated);

            // Update address
            if ($rentalProperty->address) {
                $rentalProperty->address->update([
                    'address1' => $validated['address1'],
                    'address2' => $validated['address2'],
                    'city' => $validated['city'],
                    'state' => $validated['state'],
                    'zip' => $validated['zip'],
                    'country' => $validated['country'],
                ]);
            } else {
                $rentalProperty->address()->create([
                    'address1' => $validated['address1'],
                    'address2' => $validated['address2'],
                    'city' => $validated['city'],
                    'state' => $validated['state'],
                    'zip' => $validated['zip'],
                    'country' => $validated['country'],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.rental-properties.show', $rentalProperty)
                ->with('success', 'Rental property updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update rental property: ' . $e->getMessage());
        }
    }

    public function destroy(RentalProperty $rentalProperty)
    {
        $this->authorize('delete rental property');
        
        if ($rentalProperty->activeLeases()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete rental property with active leases.');
        }

        $rentalProperty->delete();

        return redirect()->route('admin.rental-properties.index')
            ->with('success', 'Rental property deleted successfully.');
    }

    public function toggleFeatured(RentalProperty $rentalProperty)
    {
        $this->authorize('edit rental property');
        
        $rentalProperty->update(['is_featured' => !$rentalProperty->is_featured]);

        return redirect()->back()
            ->with('success', 'Featured status updated successfully.');
    }

    public function togglePublished(RentalProperty $rentalProperty)
    {
        $this->authorize('edit rental property');
        
        $rentalProperty->update([
            'is_published' => !$rentalProperty->is_published,
            'published_at' => $rentalProperty->is_published ? now() : null
        ]);

        return redirect()->back()
            ->with('success', 'Published status updated successfully.');
    }
}
