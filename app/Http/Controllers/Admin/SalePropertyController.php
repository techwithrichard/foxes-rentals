<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaleProperty;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalePropertyController extends Controller
{
    public function index()
    {
        $this->authorize('view sale property');
        
        $saleProperties = SaleProperty::with(['propertyType', 'landlord', 'address'])
            ->withCount(['inquiries', 'offers'])
            ->latest()
            ->paginate(20);

        return view('admin.sale-properties.index', compact('saleProperties'));
    }

    public function all()
    {
        $this->authorize('view sale property');
        
        $query = SaleProperty::with(['propertyType', 'landlord', 'address'])
            ->withCount(['inquiries', 'offers']);

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

        // Filter by price range
        if (request()->filled('min_price')) {
            $query->where('sale_price', '>=', request()->get('min_price'));
        }
        if (request()->filled('max_price')) {
            $query->where('sale_price', '<=', request()->get('max_price'));
        }

        // Filter by bedrooms
        if (request()->filled('bedrooms')) {
            $query->where('bedrooms', request()->get('bedrooms'));
        }

        // Filter by bathrooms
        if (request()->filled('bathrooms')) {
            $query->where('bathrooms', request()->get('bathrooms'));
        }

        $saleProperties = $query->latest()->paginate(20);
        $propertyTypes = PropertyType::active()->get();

        return view('admin.sale-properties.all', compact('saleProperties', 'propertyTypes'));
    }

    public function available()
    {
        $this->authorize('view sale property');
        
        $saleProperties = SaleProperty::with(['propertyType', 'landlord', 'address'])
            ->where('is_available', true)
            ->where('status', 'active')
            ->withCount(['inquiries', 'offers'])
            ->latest()
            ->paginate(20);

        return view('admin.sale-properties.available', compact('saleProperties'));
    }

    public function featured()
    {
        $this->authorize('view sale property');
        
        $saleProperties = SaleProperty::with(['propertyType', 'landlord', 'address'])
            ->where('is_featured', true)
            ->where('status', 'active')
            ->withCount(['inquiries', 'offers'])
            ->latest()
            ->paginate(20);

        return view('admin.sale-properties.featured', compact('saleProperties'));
    }

    public function create()
    {
        $this->authorize('create sale property');
        
        $propertyTypes = PropertyType::active()->get();
        $landlords = User::role('landlord')->get();

        return view('admin.sale-properties.create', compact('propertyTypes', 'landlords'));
    }

    public function store(Request $request)
    {
        $this->authorize('create sale property');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'property_type_id' => 'required|exists:property_types,id',
            'landlord_id' => 'nullable|exists:users,id',
            'sale_price' => 'required|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive,sold,pending',
            'furnished' => 'boolean',
            'pet_friendly' => 'boolean',
            'parking_spaces' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'images' => 'nullable|array',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'property_size' => 'nullable|numeric|min:0',
            'lot_size' => 'nullable|numeric|min:0',
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
            // Create the sale property
            $saleProperty = SaleProperty::create($validated);

            // Create address
            $saleProperty->address()->create([
                'address1' => $validated['address1'],
                'address2' => $validated['address2'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'zip' => $validated['zip'],
                'country' => $validated['country'],
            ]);

            DB::commit();

            return redirect()->route('admin.sale-properties.show', $saleProperty)
                ->with('success', 'Sale property created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create sale property: ' . $e->getMessage());
        }
    }

    public function show(SaleProperty $saleProperty)
    {
        $this->authorize('view sale property');
        
        $saleProperty->load([
            'propertyType', 'landlord', 'address', 'inquiries', 'offers.buyer'
        ]);

        return view('admin.sale-properties.show', compact('saleProperty'));
    }

    public function edit(SaleProperty $saleProperty)
    {
        $this->authorize('edit sale property');
        
        $propertyTypes = PropertyType::active()->get();
        $landlords = User::role('landlord')->get();
        $saleProperty->load('address');

        return view('admin.sale-properties.edit', compact('saleProperty', 'propertyTypes', 'landlords'));
    }

    public function update(Request $request, SaleProperty $saleProperty)
    {
        $this->authorize('edit sale property');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'property_type_id' => 'required|exists:property_types,id',
            'landlord_id' => 'nullable|exists:users,id',
            'sale_price' => 'required|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive,sold,pending',
            'furnished' => 'boolean',
            'pet_friendly' => 'boolean',
            'parking_spaces' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'images' => 'nullable|array',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'property_size' => 'nullable|numeric|min:0',
            'lot_size' => 'nullable|numeric|min:0',
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
            // Update the sale property
            $saleProperty->update($validated);

            // Update address
            if ($saleProperty->address) {
                $saleProperty->address->update([
                    'address1' => $validated['address1'],
                    'address2' => $validated['address2'],
                    'city' => $validated['city'],
                    'state' => $validated['state'],
                    'zip' => $validated['zip'],
                    'country' => $validated['country'],
                ]);
            } else {
                $saleProperty->address()->create([
                    'address1' => $validated['address1'],
                    'address2' => $validated['address2'],
                    'city' => $validated['city'],
                    'state' => $validated['state'],
                    'zip' => $validated['zip'],
                    'country' => $validated['country'],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.sale-properties.show', $saleProperty)
                ->with('success', 'Sale property updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update sale property: ' . $e->getMessage());
        }
    }

    public function destroy(SaleProperty $saleProperty)
    {
        $this->authorize('delete sale property');
        
        if ($saleProperty->offers()->where('status', 'pending')->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete sale property with pending offers.');
        }

        $saleProperty->delete();

        return redirect()->route('admin.sale-properties.index')
            ->with('success', 'Sale property deleted successfully.');
    }

    public function toggleFeatured(SaleProperty $saleProperty)
    {
        $this->authorize('edit sale property');
        
        $saleProperty->update(['is_featured' => !$saleProperty->is_featured]);

        return redirect()->back()
            ->with('success', 'Featured status updated successfully.');
    }

    public function togglePublished(SaleProperty $saleProperty)
    {
        $this->authorize('edit sale property');
        
        $saleProperty->update([
            'is_published' => !$saleProperty->is_published,
            'published_at' => $saleProperty->is_published ? now() : null
        ]);

        return redirect()->back()
            ->with('success', 'Published status updated successfully.');
    }
}
