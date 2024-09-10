<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;

//use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PDF;
use Yajra\DataTables\DataTables;

class InvoicesController extends Controller
{
    public function index()
    {
        if (\request()->ajax()) {
            $invoices = Invoice::query()
                ->with(['property:id,name', 'house:id,name'])
                ->where('tenant_id', auth()->id())
                ->latest('id')
                ->select('invoices.*');

            return DataTables::of($invoices)
                ->addColumn('actions', function ($invoice) {
                    return view('tenant.invoices.partials.actions', compact('invoice'));
                })
                ->editColumn('created_at', function ($invoice) {
                    return $invoice->created_at->format('d M, Y');
                })
                ->editColumn('amount', function ($invoice) {
                    return @setting('currency_symbol') . ' ' . number_format($invoice->amount + $invoice->bills_amount, 2);
                })
                ->editColumn('paid_amount', function ($invoice) {
                    return @setting('currency_symbol') . ' ' . number_format($invoice->paid_amount, 2);
                })
                ->addColumn('balance', function ($invoice) {
                    return @setting('currency_symbol') . ' ' . number_format(($invoice->amount + $invoice->bills_amount - $invoice->paid_amount), 2);
                })
                ->editColumn('status', function ($invoice) {
                    return view('tenant.invoices.partials.status', compact('invoice'));
                })
                ->addColumn('property', function ($invoice) {
                    return $invoice->property->name;
                })
                ->addColumn('house', function ($invoice) {
                    return $invoice->house->name;
                })
                ->rawColumns(['actions', 'status'])
                ->setRowClass('nk-tb-item')
                ->make(true);

        }
        return view('tenant.invoices.index');
    }


    public function show($id)
    {
        $invoice = Invoice::with('payments')
            ->where('tenant_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();
        $pdf = Pdf::loadView('tenant.invoices.show', compact('invoice'));
        $invoice_name = 'invoice_' . auth()->user()->name . '_' . $invoice->id . '.pdf';
        return $pdf->stream($invoice_name);
    }


}
