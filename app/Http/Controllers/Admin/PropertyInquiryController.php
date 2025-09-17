<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PropertyInquiry;
use App\Models\User;
use Illuminate\Http\Request;

class PropertyInquiryController extends Controller
{
    public function index()
    {
        $this->authorize('view property inquiry');
        
        $inquiries = PropertyInquiry::with(['property', 'assignedUser'])
            ->latest()
            ->paginate(20);

        return view('admin.property-inquiries.index', compact('inquiries'));
    }

    public function create()
    {
        $this->authorize('create property inquiry');
        
        return view('admin.property-inquiries.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create property inquiry');
        
        $validated = $request->validate([
            'property_id' => 'required|string',
            'property_type' => 'required|in:rental_property,sale_property,lease_property',
            'inquirer_name' => 'required|string|max:255',
            'inquirer_email' => 'required|email|max:255',
            'inquirer_phone' => 'required|string|max:20',
            'message' => 'required|string|max:2000',
            'priority' => 'required|in:low,medium,high,urgent',
            'source' => 'nullable|string|max:100',
            'budget_range' => 'nullable|string|max:100',
            'move_in_date' => 'nullable|date|after:today',
            'special_requirements' => 'nullable|array',
        ]);

        $validated['status'] = 'pending';
        $validated['assigned_to'] = auth()->id();

        PropertyInquiry::create($validated);

        return redirect()->route('admin.property-inquiries.index')
            ->with('success', 'Property inquiry created successfully.');
    }

    public function show(PropertyInquiry $propertyInquiry)
    {
        $this->authorize('view property inquiry');
        
        $propertyInquiry->load(['property', 'assignedUser']);

        return view('admin.property-inquiries.show', compact('propertyInquiry'));
    }

    public function edit(PropertyInquiry $propertyInquiry)
    {
        $this->authorize('edit property inquiry');
        
        $users = User::role(['admin', 'property_manager', 'leasing_agent', 'sales_agent'])->get();

        return view('admin.property-inquiries.edit', compact('propertyInquiry', 'users'));
    }

    public function update(Request $request, PropertyInquiry $propertyInquiry)
    {
        $this->authorize('edit property inquiry');
        
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,qualified,unqualified,closed',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'response' => 'nullable|string|max:2000',
            'follow_up_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($request->filled('response')) {
            $validated['response_date'] = now();
        }

        $propertyInquiry->update($validated);

        return redirect()->route('admin.property-inquiries.show', $propertyInquiry)
            ->with('success', 'Property inquiry updated successfully.');
    }

    public function destroy(PropertyInquiry $propertyInquiry)
    {
        $this->authorize('delete property inquiry');
        
        $propertyInquiry->delete();

        return redirect()->route('admin.property-inquiries.index')
            ->with('success', 'Property inquiry deleted successfully.');
    }

    public function assign(Request $request, PropertyInquiry $propertyInquiry)
    {
        $this->authorize('edit property inquiry');
        
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $propertyInquiry->update($validated);

        return redirect()->back()
            ->with('success', 'Inquiry assigned successfully.');
    }

    public function qualify(PropertyInquiry $propertyInquiry)
    {
        $this->authorize('edit property inquiry');
        
        $propertyInquiry->update([
            'is_qualified' => !$propertyInquiry->is_qualified,
            'status' => $propertyInquiry->is_qualified ? 'qualified' : 'unqualified'
        ]);

        return redirect()->back()
            ->with('success', 'Inquiry qualification status updated.');
    }
}
