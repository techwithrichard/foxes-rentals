<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomInvoice;
use App\Models\Property;
use App\Models\Voucher;
use App\Notifications\LandlordInvoiceCancelledNotification;
//use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use PDF;

class CustomInvoiceController extends Controller
{

    public function index()
    {

        abort_unless(auth()->user()->can('view custom invoice'), 403);
        if (\request()->ajax()) {
            $invoices = CustomInvoice::with('landlord:id,name', 'property:id,name', 'house:id,name')
                ->withSum('items', 'cost');
            return DataTables::of($invoices)
                ->filter(function ($query) {
                    if (request()->filled('property_filter')) {
                        $query->where('property_id', request()->get('property_filter'));
                    }
                    if (request()->filled('date_filter')) {
                        $query->where('invoice_date', request()->get('date_filter'));
                    }
                }, true)
                ->addColumn('actions', function ($invoice) {
                    return view('admin.custom_invoices.action', compact('invoice'));
                })
                ->editColumn('invoice_date', function ($invoice) {
                    return $invoice->invoice_date->format('d M, Y');
                })
                ->editColumn('due_date', function ($invoice) {
                    return $invoice->due_date?->format('d M, Y');
                })
                ->editColumn('landlord', function ($invoice) {
                    return $invoice->landlord->name;
                })
                ->editColumn('property', function ($invoice) {
                    return $invoice->property->name;
                })
                ->addColumn('house', function ($invoice) {
                    return $invoice->house?->name;
                })
                ->addColumn('total_amount', function ($invoice) {
                    return setting('currency_symbol') . ' ' . number_format($invoice->items_sum_cost, 2);
                })
                ->rawColumns(['action'])
                ->make(true);

        }

        $properties = Property::pluck('name', 'id');
        return view('admin.custom_invoices.index', compact('properties'));
    }


    public function create()
    {
        abort_unless(auth()->user()->can('create custom invoice'), 403);
        return view('admin.custom_invoices.create');
    }


    public function show($id)
    {
        abort_unless(auth()->user()->can('view custom invoice'), 403);
        $invoice = CustomInvoice::query()
            ->with('landlord', 'items', 'property:id,name', 'house:id,name')
            ->findOrFail($id);

        return view('admin.custom_invoices.show', compact('invoice'));
    }


    public function destroy($id)
    {

        abort_unless(auth()->user()->can('delete custom invoice'), 403);
        $invoice = CustomInvoice::query()
            ->with('landlord')
            ->withSum('items', 'cost')
            ->findOrFail($id);

        DB::transaction(function () use ($invoice) {
            $invoice->delete();
            $data = [
                'invoice_id' => $invoice->invoice_id,
                'amount' => setting('currency_symbol') . ' ' . number_format($invoice->items_sum_cost, 2),
            ];
            $invoice->landlord->notify(new LandlordInvoiceCancelledNotification($data));
        });

        return redirect()->back()->with('success', __('Invoice deleted successfully'));
    }

    public function print($id)
    {
        abort_unless(auth()->user()->can('view custom invoice'), 403);
        $invoice = CustomInvoice::query()
            ->with('landlord', 'items', 'property:id,name', 'house:id,name')
            ->findOrFail($id);
        $pdf = PDF::loadView('admin.custom_invoices.print', compact('invoice'));
        return $pdf->stream('invoice.pdf');

    }
}
