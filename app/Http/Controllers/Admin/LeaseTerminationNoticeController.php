<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LeaseTerminationNoticeController extends Controller
{

    public function index()
    {
        if (\request()->ajax()) {
            $leases = Lease::query()
                ->with('tenant:id,name', 'property:id,name', 'house:id,name')
                ->withSum('bills', 'amount')
                ->whereNotNull('leases.termination_date_notice')
                ->select('leases.*');

            return DataTables::of($leases)
                ->addIndexColumn()
                ->addColumn('actions', function ($lease) {
                    return view('landlord.lease.partials.actions', compact('lease'));
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
                ->editColumn('rent', function ($lease) {
                    return @setting('currency_symbol') . ' ' . number_format($lease->rent, 2);
                })
                ->addColumn('total_bills', function ($lease) {
                    return @setting('currency_symbol') . ' ' . number_format($lease->bills_sum_amount, 2);
                })
                ->editColumn('start_date', function ($lease) {
                    return $lease->start_date->format('d M Y');
                })
                ->editColumn('termination_date_notice', function ($lease) {
                    return $lease->termination_date_notice->format('d M Y');
                })
                ->setRowClass('nk-tb-item')
                ->rawColumns(['actions'])
                ->toJson();
        }
        return view('landlord.lease_notices.index');
    }


}
