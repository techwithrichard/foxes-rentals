<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PropertyStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\HouseRequest;
use App\Models\House;
use App\Models\Property;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class HouseController extends Controller
{

    public function index()
    {
        abort_unless(auth()->user()->can('view house'), 403);
        if (\request()->ajax()) {
            $houses = House::with('property:id,name', 'lease.tenant:id,name', 'landlord:id,name')
                ->select('houses.*')
                ->latest();


            return DataTables::of($houses)
                ->filter(function ($query) {
                    if (request()->filled('status_filter')) {
                        $query->where('status', request()->get('status_filter'));
                    }
                    if (request()->filled('property_filter')) {
                        $query->where('property_id', request()->get('property_filter'));
                    }
                }, true)
                ->addIndexColumn()
                ->addColumn('actions', function ($house) {
                    return view('admin.house.partials.actions', compact('house'));
                })
                ->editColumn('status', function ($house) {
                    return view('admin.house.partials.status', compact('house'));
                })
                //edit rent column
                ->editColumn('rent', function ($house) {
                    return number_format($house->rent, 2);
                })
                ->addColumn('rent_status', function ($house) {
                    return view('admin.house.partials.rent', compact('house'));
                })
                ->addColumn('tenant', function ($house) {
                    return $house->lease->tenant->name ?? 'N/A';
                })
                ->addColumn('landlord', function ($house) {
                    return $house->landlord->name ?? 'N/A';
                })
                ->addColumn('property', function ($house) {
                    return $house->property->name;
                })
                ->rawColumns(['actions', 'is_vacant','rent_status'])
                ->make(true);

        }

        //get properties where status=PropertyStatusEnum::MULTI_UNIT
        $properties = Property::where('status', PropertyStatusEnum::MULTI_UNIT)->pluck('name', 'id');
        return view('admin.house.index', compact('properties'));
    }


    public function create()
    {
        abort_unless(auth()->user()->can('create house'), 403);
        return view('admin.house.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(HouseRequest $request)
    {
        // Validation is handled by HouseRequest
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Create the house
            $house = House::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'property_id' => $validated['property_id'],
                'rent' => $validated['rent'],
                'deposit' => $validated['deposit'],
                'landlord_id' => $validated['landlord_id'],
                'status' => $validated['status'],
                'is_vacant' => $validated['is_vacant'] ?? true,
                'bedrooms' => $validated['bedrooms'],
                'bathrooms' => $validated['bathrooms'],
                'size' => $validated['size'],
                'house_type_id' => $validated['house_type_id'],
            ]);

            DB::commit();

            return redirect()
                ->route('admin.house.show', $house)
                ->with('success', __('House created successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('Failed to create house: ') . $e->getMessage());
        }
    }


    public function show($id)
    {
        abort_unless(auth()->user()->can('view house'), 403);
        $house = House::with('property.address', 'lease.tenant:id,name', 'landlord:id,name')
            ->with(['leases' => function ($query) {
                $query->orderBy('id', 'desc');
            }])
            ->findOrFail($id);
        return view('admin.house.show', compact('house'));
    }


    public function edit($id)
    {
        abort_unless(auth()->user()->can('edit house'), 403);
        return view('admin.house.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
