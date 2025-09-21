<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PropertyTypeController extends Controller
{
    public function index()
    {
        $this->authorize('manage property types');
        
        $propertyTypes = PropertyType::withCount(['properties', 'rentalProperties', 'saleProperties', 'leaseProperties'])
            ->orderBy('sort_order')
            ->paginate(20);

        return view('admin.property-types.index', compact('propertyTypes'));
    }

    public function create()
    {
        $this->authorize('manage property types');
        
        return view('admin.property-types.create');
    }

    public function store(Request $request)
    {
        $this->authorize('manage property types');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:property_types',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:residential,commercial,industrial,land,mixed-use',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        PropertyType::create($validated);

        return redirect()->route('admin.property-types.index')
            ->with('success', 'Property type created successfully.');
    }

    public function show(PropertyType $propertyType)
    {
        $this->authorize('manage property types');
        
        $propertyType->load(['properties', 'rentalProperties', 'saleProperties', 'leaseProperties']);
        
        return view('admin.property-types.show', compact('propertyType'));
    }

    public function edit(PropertyType $propertyType)
    {
        $this->authorize('manage property types');
        
        return view('admin.property-types.edit', compact('propertyType'));
    }

    public function update(Request $request, PropertyType $propertyType)
    {
        $this->authorize('manage property types');
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('property_types')->ignore($propertyType->id)],
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:residential,commercial,industrial,land,mixed-use',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $propertyType->update($validated);

        return redirect()->route('admin.property-types.index')
            ->with('success', 'Property type updated successfully.');
    }

    public function destroy(PropertyType $propertyType)
    {
        $this->authorize('manage property types');
        
        if ($propertyType->properties()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete property type with associated properties.');
        }

        $propertyType->delete();

        return redirect()->route('admin.property-types.index')
            ->with('success', 'Property type deleted successfully.');
    }

    public function toggleStatus(PropertyType $propertyType)
    {
        $this->authorize('manage property types');
        
        $propertyType->update(['is_active' => !$propertyType->is_active]);

        return redirect()->back()
            ->with('success', 'Property type status updated successfully.');
    }
}
