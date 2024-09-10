<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\CustomInvoice;
use App\Models\Property;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class InvoicesController extends Controller
{

    public function index()
    {
        if (\request()->ajax()) {
            $invoices = CustomInvoice::with('property:id,name')
                ->withSum('items', 'cost')
                ->where('landlord_id', auth()->user()->id)
                ->latest();
            return DataTables::of($invoices)
                ->filter(function ($query) {
                    if (request()->filled('date_filter')) {
                        $query->where('invoice_date', request()->get('date_filter'));
                    }
                }, true)
                ->addColumn('actions', function ($invoice) {
                    return view('landlord.invoices.actions', compact('invoice'));
                })
                ->editColumn('invoice_date', function ($invoice) {
                    return $invoice->invoice_date->format('d M, Y');
                })
                ->editColumn('due_date', function ($invoice) {
                    return $invoice->due_date?->format('d M, Y');
                })
                ->editColumn('property', function ($invoice) {
                    return $invoice->property->name;
                })
                ->addColumn('total_amount', function ($invoice) {
                    return setting('currency_symbol') . ' ' . $invoice->items_sum_cost;
                })
                ->rawColumns(['action'])
                ->make(true);

        }

        return view('landlord.invoices.index');
    }

    public function show($id)
    {
        $invoice = CustomInvoice::query()
            ->with('landlord', 'items', 'property:id,name', 'house:id,name')
//            ->where('landlord_id', auth()->user()->id)
            ->findOrFail($id);

        return view('landlord.invoices.show', compact('invoice'));
    }

    public function print($id)
    {
        $invoice = CustomInvoice::query()
            ->with('landlord', 'items', 'property:id,name', 'house:id,name')
            ->findOrFail($id);
        $pdf = PDF::loadView('landlord.invoices.print', compact('invoice'));
        return $pdf->stream('invoice.pdf');

    }


}
