<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyRequest;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PropertyController extends Controller
{

    public function index()
    {
        abort_unless(auth()->user()->can('view property'), 403);
        if (\request()->ajax()) {
            $properties = Property::query()
                ->with([
                    'lease.tenant:id,name',
                    'landlord:id,name',
                    'address'
                ])
                ->select('properties.*')
                ->withCount('houses')
                ->latest('id');


            return DataTables::of($properties)
                ->filter(function ($query) {
                    if (request()->filled('status_filter')) {
                        $query->where('status', request()->get('status_filter'));
                    }
                }, true)
                ->addIndexColumn()
                ->addColumn('actions', function ($property) {
                    return view('admin.property.partials.actions', compact('property'));
                })
                ->addColumn('status', function ($property) {
                    return view('admin.property.partials.status', compact('property'));
                })
                ->addColumn('tenant', function ($property) {
                    return $property->lease->tenant->name ?? 'N/A';
                })
                ->addColumn('details', function ($property) {
                    return view('admin.property.partials.details', compact('property'));
                })
                ->addColumn('landlord', function ($property) {
                    return $property->landlord->name ?? 'N/A';
                })
                ->addColumn('address', function ($property) {
                    return $property->address ? $property->address->city . ', ' . $property->address->state : 'N/A';
                })
                ->rawColumns(['actions', 'status', 'details'])
                ->make(true);
        }

        return view('admin.property.index');
    }


    public function create()
    {
        abort_unless(auth()->user()->can('create property'), 403);
        //extract type from the request
        $type = request()->get('type');
        return view('admin.property.create', compact('type'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(PropertyRequest $request)
    {
        // Validation is handled by PropertyRequest
        $validated = $request->validated();

        try {
            DB::beginTransaction();

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

            return redirect()
                ->route('admin.property.show', $property)
                ->with('success', __('Property created successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('Failed to create property: ') . $e->getMessage());
        }
    }


    public function show($id)
    {
        abort_unless(auth()->user()->can('view property'), 403);
        $property = Property::with('address', 'lease')->findOrFail($id);
        return view('admin.property.show', compact('property'));
    }
  

    public function edit($id)
    {
        abort_unless(auth()->user()->can('edit property'), 403);
        $property = Property::with('address', 'landlord')->findOrFail($id);
        return view('admin.property.edit', compact('property'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(PropertyRequest $request, $id)
    {
        $property = Property::findOrFail($id);

        // Validation is handled by PropertyRequest
        $validated = $request->validated();

        try {
            DB::beginTransaction();

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

            return redirect()
                ->route('admin.property.show', $property)
                ->with('success', __('Property updated successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('Failed to update property: ') . $e->getMessage());
        }
    }


    public function destroy($id)
    {
        abort_unless(auth()->user()->can('delete property'), 403);
        $property = Property::findOrFail($id);

        //transaction
        DB::transaction(function () use ($property) {
            // Soft delete related records first
            $property->leases()->delete();
            $property->houses()->delete();
            // Then soft delete the property itself
            $property->delete();
        });

        return redirect()->back()->with('success', __('Property deleted successfully'));
    }
}
