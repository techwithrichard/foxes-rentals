<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Overpayment;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class OverpaymentsController extends Controller
{

    public function index()
    {
        abort_unless(auth()->user()->can('view overpayment'), 403);
        if (\request()->ajax()) {

            $overpayments = Overpayment::with('tenant:id,name')->latest();

            return DataTables::of($overpayments)
                ->addIndexColumn()
                ->addColumn('action', function ($overpayment) {
                    return view('admin.overpayments.action', compact('overpayment'));
                })
                ->editColumn('amount', function ($overpayment) {
                    return number_format($overpayment->amount, 2);
                })
                ->addColumn('tenant', function ($overpayment) {
                    return $overpayment->tenant->name;
                })
                ->editColumn('created_at', function ($overpayment) {
                    return $overpayment->created_at->format('d M Y, h:i A');
                })
                ->rawColumns(['action'])
                ->setRowClass('nk-tb-item')
                ->make(true);
        }

        $sum_overpayments = Overpayment::sum('amount');

        return view('admin.overpayments.index', compact('sum_overpayments'));
    }


    public function destroy($id)
    {
        abort_unless(auth()->user()->can('delete overpayment'), 403);
        Overpayment::destroy($id);

        return back()->with('success', __('Overpayment deleted successfully'));
    }
}
