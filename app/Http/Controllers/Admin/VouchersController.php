<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Voucher;
use App\Notifications\LandlordVoucherCancelledNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use PDF;

class VouchersController extends Controller
{

    public function index()
    {
        abort_unless(auth()->user()->can('view landlord voucher'), 403);
        if (\request()->ajax()) {
            $vouchers = Voucher::query()
                ->with('landlord:id,name', 'items', 'property:id,name', 'house:id,name')
                ->select('vouchers.*')
                ->withSum('items', 'cost')
                ->latest();

            return DataTables::of($vouchers)
                ->filter(function ($query) {
                    if (request()->filled('voucher_type_filter')) {
                        $query->where('type', request()->get('voucher_type_filter'));
                    }
                    if (request()->filled('property_filter')) {
                        $query->where('property_id', request()->get('property_filter'));
                    }
                    if (request()->filled('date_filter')) {
                        $query->where('voucher_date', request()->get('date_filter'));
                    }
                }, true)
                ->editColumn('voucher_date', function ($voucher) {
                    return $voucher->voucher_date->format('d/m/Y');
                })
                ->addColumn('property', function ($voucher) {
                    return $voucher->property->name ?? '';
                })
                ->addColumn('house', function ($voucher) {
                    return $voucher->house->name ?? '';
                })
                ->editColumn('type', function ($voucher) {
                    return view('admin.vouchers.partials.type', compact('voucher'))->render();
                })
                ->addColumn('amount', function ($voucher) {
                    return $voucher->items_sum_cost;
                })
                ->addColumn('landlord', function ($voucher) {
                    return $voucher->landlord->name ?? '';
                })
                ->addColumn('actions', function ($voucher) {
                    return view('admin.vouchers.partials.actions', compact('voucher'))->render();
                })
                ->rawColumns(['actions', 'type'])
                ->make(true);
        }


        $properties = Property::pluck('name', 'id');
        return view('admin.vouchers.index', compact('properties'));
    }


    public function create()
    {
        abort_unless(auth()->user()->can('create landlord voucher'), 403);
        return view('admin.vouchers.create');
    }


    public function show($id)
    {
        abort_unless(auth()->user()->can('view landlord voucher'), 403);
        $voucher = Voucher::query()
            ->with('landlord', 'items', 'property:id,name', 'house:id,name')
            ->findOrFail($id);

        return view('admin.vouchers.show', compact('voucher'));


    }

    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        abort_unless(auth()->user()->can('delete landlord voucher'), 403);
        $voucher = Voucher::query()
            ->with('landlord')
            ->withSum('items', 'cost')
            ->findOrFail($id);

        DB::transaction(function () use ($voucher) {
            $voucher->delete();
            $data = [
                'voucher_id' => $voucher->voucher_id,
                'amount' => setting('currency_symbol') . ' ' . number_format($voucher->items_sum_cost, 2),
            ];
            $voucher->landlord->notify(new LandlordVoucherCancelledNotification($data));
        });

        return redirect()->route('admin.vouchers.index')
            ->with('success', __('Voucher deleted successfully'));
    }

    public function print($id)
    {
        abort_unless(auth()->user()->can('view landlord voucher'), 403);
        $voucher = Voucher::query()
            ->with('landlord', 'items', 'property:id,name', 'house:id,name')
            ->findOrFail($id);
        $pdf = PDF::loadView('admin.vouchers.print', compact('voucher'));
        return $pdf->stream('voucher.pdf');
    }
}
