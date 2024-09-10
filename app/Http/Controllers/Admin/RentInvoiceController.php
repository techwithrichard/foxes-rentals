<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;

//use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PDF;
use Yajra\DataTables\DataTables;

class RentInvoiceController extends Controller
{

    public function index()
    {
        abort_unless(auth()->user()->can('view invoice'), 403);
        if (\request()->ajax()) {
            $invoices = Invoice::query()
                ->with(['tenant:id,name', 'property:id,name', 'house:id,name'])
                ->latest('id')
                ->select('invoices.*')
                ->withSum('verified_payments', 'amount');


            return DataTables::of($invoices)
                ->addColumn('actions', function ($invoice) {
                    return view('admin.invoice.rent.partials.actions', compact('invoice'));
                })
                ->editColumn('created_at', function ($invoice) {
                    return $invoice->created_at?->format('d M, Y');
                })
                ->addColumn('total_amount', function ($invoice) {
                    return @setting('currency_symbol') . ' ' . number_format($invoice->amount + $invoice->bills_amount, 2);
                })
                ->addColumn('paid_amount', function ($invoice) {
                    return @setting('currency_symbol') . ' ' . number_format($invoice->verified_payments_sum_amount, 2);
                })
                ->editColumn('status', function ($invoice) {
                    return view('admin.invoice.rent.partials.status', compact('invoice'));
                })
                ->addColumn('property', function ($invoice) {
                    return $invoice->property?->name;
                })
                ->addColumn('house', function ($invoice) {
                    return $invoice->house?->name;
                })
                ->addColumn('balance', function ($invoice) {
                    return @setting('currency_symbol') . ' '
                        . number_format(($invoice->amount + $invoice->bills_amount) - $invoice->verified_payments_sum_amount, 2);
                })
                ->addColumn('tenant', function ($invoice) {
                    return $invoice->tenant->name;
                })
                ->rawColumns(['actions', 'status'])
//                ->setRowClass('nk-tb-item')
                ->make(true);

        }

        return view('admin.invoice.rent.index');
    }

    public function show($id)
    {
        abort_unless(auth()->user()->can('view invoice'), 403);
        $invoice = Invoice::query()
            ->with('tenant:id,name,email,phone,address', 'property:id,name', 'house:id,name', 'verified_payments', 'cancelled_payments', 'pending_payments')
            ->findOrFail($id);

        return view('admin.invoice.rent.show', compact('invoice'));
    }

    public function edit($id)
    {

        return view('admin.invoice.rent.edit', ['invoice_id' => $id]);

    }

    public function print($id)
    {
        abort_unless(auth()->user()->can('view invoice'), 403);
        $invoice = Invoice::query()
            ->with('tenant:id,name,email,phone,address', 'property:id,name', 'house:id,name', 'verified_payments', 'cancelled_payments', 'pending_payments')
            ->findOrFail($id);
        $pdf = PDF::loadView('admin.invoice.rent.print', compact('invoice'));

        // $invoice_name = tenant_name_invoice_id rent invoice.pdf
        $invoice_name = $invoice->tenant->name . '_' . $invoice->invoice_id . ' rent invoice.pdf';
        return $pdf->stream($invoice_name);
    }
}
