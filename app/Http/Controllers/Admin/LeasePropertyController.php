<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaseProperty;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeasePropertyController extends Controller
{
    public function index()
    {
        $this->authorize('view lease property');
        
        $leaseProperties = LeaseProperty::with(['propertyType', 'landlord', 'address'])
            ->withCount(['leaseAgreements', 'inquiries', 'applications'])
            ->latest()
            ->paginate(20);

        return view('admin.lease-properties.index', compact('leaseProperties'));
    }

    public function all()
    {
        $this->authorize('view lease property');
        
        $query = LeaseProperty::with(['propertyType', 'landlord', 'address'])
            ->withCount(['leaseAgreements', 'inquiries', 'applications']);

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

        // Filter by availability
        if (request()->filled('availability')) {
            $query->where('is_available', request()->get('availability') === 'available');
        }

        // Filter by property type
        if (request()->filled('property_type')) {
            $query->where('property_type_id', request()->get('property_type'));
        }

        // Filter by lease amount range
        if (request()->filled('min_lease_amount')) {
            $query->where('lease_amount', '>=', request()->get('min_lease_amount'));
        }
        if (request()->filled('max_lease_amount')) {
            $query->where('lease_amount', '<=', request()->get('max_lease_amount'));
        }

        // Filter by lease duration
        if (request()->filled('lease_duration')) {
            $query->where('lease_duration_months', request()->get('lease_duration'));
        }

        $leaseProperties = $query->latest()->paginate(20);
        $propertyTypes = PropertyType::active()->get();

        return view('admin.lease-properties.all', compact('leaseProperties', 'propertyTypes'));
    }

    public function available()
    {
        $this->authorize('view lease property');
        
        $leaseProperties = LeaseProperty::with(['propertyType', 'landlord', 'address'])
            ->where('is_available', true)
            ->where('status', 'active')
            ->withCount(['leaseAgreements', 'inquiries', 'applications'])
            ->latest()
            ->paginate(20);

        return view('admin.lease-properties.available', compact('leaseProperties'));
    }

    public function create()
    {
        $this->authorize('create lease property');
        
        $propertyTypes = PropertyType::active()->get();
        $landlords = User::role('landlord')->get();

        return view('admin.lease-properties.create', compact('propertyTypes', 'landlords'));
    }

    public function store(Request $request)
    {
        $this->authorize('create lease property');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'property_type_id' => 'required|exists:property_types,id',
            'landlord_id' => 'nullable|exists:users,id',
            'lease_amount' => 'required|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive,maintenance',
            'lease_duration_months' => 'required|integer|min:1',
            'minimum_lease_period' => 'nullable|integer|min:1',
            'maximum_lease_period' => 'nullable|integer|min:1',
            'deposit_amount' => 'nullable|numeric|min:0',
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
            'lease_terms' => 'nullable|array',
            'special_conditions' => 'nullable|array',
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
            // Create the lease property
            $leaseProperty = LeaseProperty::create($validated);

            // Create address
            $leaseProperty->address()->create([
                'address1' => $validated['address1'],
                'address2' => $validated['address2'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'zip' => $validated['zip'],
                'country' => $validated['country'],
            ]);

            DB::commit();

            return redirect()->route('admin.lease-properties.show', $leaseProperty)
                ->with('success', 'Lease property created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create lease property: ' . $e->getMessage());
        }
    }

    public function show(LeaseProperty $leaseProperty)
    {
        $this->authorize('view lease property');
        
        $leaseProperty->load([
            'propertyType', 'landlord', 'address', 'leaseAgreements.tenant',
            'inquiries', 'applications.applicant'
        ]);

        return view('admin.lease-properties.show', compact('leaseProperty'));
    }

    public function edit(LeaseProperty $leaseProperty)
    {
        $this->authorize('edit lease property');
        
        $propertyTypes = PropertyType::active()->get();
        $landlords = User::role('landlord')->get();
        $leaseProperty->load('address');

        return view('admin.lease-properties.edit', compact('leaseProperty', 'propertyTypes', 'landlords'));
    }

    public function update(Request $request, LeaseProperty $leaseProperty)
    {
        $this->authorize('edit lease property');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'property_type_id' => 'required|exists:property_types,id',
            'landlord_id' => 'nullable|exists:users,id',
            'lease_amount' => 'required|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive,maintenance',
            'lease_duration_months' => 'required|integer|min:1',
            'minimum_lease_period' => 'nullable|integer|min:1',
            'maximum_lease_period' => 'nullable|integer|min:1',
            'deposit_amount' => 'nullable|numeric|min:0',
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
            'lease_terms' => 'nullable|array',
            'special_conditions' => 'nullable|array',
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
            // Update the lease property
            $leaseProperty->update($validated);

            // Update address
            if ($leaseProperty->address) {
                $leaseProperty->address->update([
                    'address1' => $validated['address1'],
                    'address2' => $validated['address2'],
                    'city' => $validated['city'],
                    'state' => $validated['state'],
                    'zip' => $validated['zip'],
                    'country' => $validated['country'],
                ]);
            } else {
                $leaseProperty->address()->create([
                    'address1' => $validated['address1'],
                    'address2' => $validated['address2'],
                    'city' => $validated['city'],
                    'state' => $validated['state'],
                    'zip' => $validated['zip'],
                    'country' => $validated['country'],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.lease-properties.show', $leaseProperty)
                ->with('success', 'Lease property updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update lease property: ' . $e->getMessage());
        }
    }

    public function destroy(LeaseProperty $leaseProperty)
    {
        $this->authorize('delete lease property');
        
        if ($leaseProperty->leaseAgreements()->where('status', 'active')->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete lease property with active lease agreements.');
        }

        $leaseProperty->delete();

        return redirect()->route('admin.lease-properties.index')
            ->with('success', 'Lease property deleted successfully.');
    }

    public function toggleFeatured(LeaseProperty $leaseProperty)
    {
        $this->authorize('edit lease property');
        
        $leaseProperty->update(['is_featured' => !$leaseProperty->is_featured]);

        return redirect()->back()
            ->with('success', 'Featured status updated successfully.');
    }

    public function togglePublished(LeaseProperty $leaseProperty)
    {
        $this->authorize('edit lease property');
        
        $leaseProperty->update([
            'is_published' => !$leaseProperty->is_published,
            'published_at' => $leaseProperty->is_published ? now() : null
        ]);

        return redirect()->back()
            ->with('success', 'Published status updated successfully.');
    }
}
