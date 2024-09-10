<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Voucher;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VouchersController extends Controller
{

    public function index()
    {
        if (\request()->ajax()) {
            $vouchers = Voucher::query()
                ->with('items', 'property:id,name', 'house:id,name')
                ->select('vouchers.*')
                ->withSum('items', 'cost')
                ->where('landlord_id', auth()->user()->id)
                ->latest();

            return DataTables::of($vouchers)
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
                ->addColumn('actions', function ($voucher) {
                    return view('landlord.vouchers.partials.actions', compact('voucher'))->render();
                })
                ->rawColumns(['actions', 'type'])
                ->make(true);
        }


        return view('landlord.vouchers.index');
    }


    public function show($id)
    {
        $voucher = Voucher::query()
            ->with('landlord', 'items', 'property:id,name', 'house:id,name')
            ->findOrFail($id);

        return view('landlord.vouchers.show', compact('voucher'));
    }

    public function print($id)
    {
        $voucher = Voucher::query()
            ->with('landlord', 'items', 'property:id,name', 'house:id,name')
            ->findOrFail($id);
        $pdf = PDF::loadView('admin.vouchers.print', compact('voucher'));
        return $pdf->stream('voucher.pdf');
    }


}
