<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentalUnit;
use App\Models\RentalProperty;
use Illuminate\Http\Request;

class RentalUnitController extends Controller
{
    public function index()
    {
        $this->authorize('view rental unit');
        
        $rentalUnits = RentalUnit::with(['rentalProperty.propertyType', 'rentalProperty.address'])
            ->withCount(['leases', 'maintenanceRequests'])
            ->latest()
            ->paginate(20);

        return view('admin.rental-units.index', compact('rentalUnits'));
    }

    public function create()
    {
        $this->authorize('create rental unit');
        
        $rentalProperties = RentalProperty::active()->get();

        return view('admin.rental-units.create', compact('rentalProperties'));
    }

    public function store(Request $request)
    {
        $this->authorize('create rental unit');
        
        $validated = $request->validate([
            'rental_property_id' => 'required|exists:rental_properties,id',
            'unit_number' => 'required|string|max:50',
            'unit_name' => 'nullable|string|max:255',
            'floor_number' => 'nullable|integer|min:0',
            'rent_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,maintenance',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'square_footage' => 'nullable|numeric|min:0',
            'balcony' => 'boolean',
            'parking_space' => 'boolean',
            'storage_unit' => 'boolean',
            'features' => 'nullable|array',
            'images' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
            'maintenance_notes' => 'nullable|string|max:1000',
        ]);

        RentalUnit::create($validated);

        return redirect()->route('admin.rental-units.index')
            ->with('success', 'Rental unit created successfully.');
    }

    public function show(RentalUnit $rentalUnit)
    {
        $this->authorize('view rental unit');
        
        $rentalUnit->load([
            'rentalProperty.propertyType', 'rentalProperty.address',
            'leases.tenant', 'maintenanceRequests'
        ]);

        return view('admin.rental-units.show', compact('rentalUnit'));
    }

    public function edit(RentalUnit $rentalUnit)
    {
        $this->authorize('edit rental unit');
        
        $rentalProperties = RentalProperty::active()->get();

        return view('admin.rental-units.edit', compact('rentalUnit', 'rentalProperties'));
    }

    public function update(Request $request, RentalUnit $rentalUnit)
    {
        $this->authorize('edit rental unit');
        
        $validated = $request->validate([
            'rental_property_id' => 'required|exists:rental_properties,id',
            'unit_number' => 'required|string|max:50',
            'unit_name' => 'nullable|string|max:255',
            'floor_number' => 'nullable|integer|min:0',
            'rent_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,maintenance',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'square_footage' => 'nullable|numeric|min:0',
            'balcony' => 'boolean',
            'parking_space' => 'boolean',
            'storage_unit' => 'boolean',
            'features' => 'nullable|array',
            'images' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
            'maintenance_notes' => 'nullable|string|max:1000',
        ]);

        $rentalUnit->update($validated);

        return redirect()->route('admin.rental-units.show', $rentalUnit)
            ->with('success', 'Rental unit updated successfully.');
    }

    public function destroy(RentalUnit $rentalUnit)
    {
        $this->authorize('delete rental unit');
        
        if ($rentalUnit->leases()->where('status', 'active')->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete rental unit with active leases.');
        }

        $rentalUnit->delete();

        return redirect()->route('admin.rental-units.index')
            ->with('success', 'Rental unit deleted successfully.');
    }

    public function toggleStatus(RentalUnit $rentalUnit)
    {
        $this->authorize('edit rental unit');
        
        $newStatus = $rentalUnit->status === 'active' ? 'inactive' : 'active';
        $rentalUnit->update(['status' => $newStatus]);

        return redirect()->back()
            ->with('success', 'Rental unit status updated successfully.');
    }
}
