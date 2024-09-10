<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use App\Models\Property;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LeaseHistoryController extends Controller
{

    public function index()
    {
        abort_unless(auth()->user()->can('view lease history'), 403);
        if (\request()->ajax()) {
            $leases = Lease::onlyTrashed()
                ->with('tenant:id,name', 'property:id,name', 'house:id,name')
                ->withSum('bills', 'amount')
                ->select('leases.*');

            return DataTables::of($leases)
                ->filter(function ($query) {
                    if (request()->filled('property_filter')) {
                        $query->where('property_id', request()->get('property_filter'));
                    }
                }, true)
                ->addIndexColumn()
                ->addColumn('actions', function ($lease) {
                    return view('admin.lease_history.actions', compact('lease'));
                })
                ->addColumn('tenant', function ($lease) {
                    return $lease->tenant->name;
                })
                ->addColumn('property', function ($lease) {
                    return $lease->property->name;
                })
                ->addColumn('house', function ($lease) {
                    return $lease->house->name;
                })
                ->addColumn('total_bills', function ($lease) {
                    return number_format($lease->bills_sum_amount, 2);
                })
                ->editColumn('start_date', function ($lease) {
                    return $lease->start_date->format('d M Y');
                })
                ->editColumn('deleted_at', function ($lease) {
                    return $lease->deleted_at->format('d M Y');
                })
                ->setRowClass('nk-tb-item')
                ->rawColumns(['actions'])
                ->toJson();
        }

        $properties = Property::orderBy('name')->pluck('name', 'id');
        return view('admin.lease_history.index', compact('properties'));
    }


    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        abort_unless(auth()->user()->can('delete lease history'), 403);
    }
}
