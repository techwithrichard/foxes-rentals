<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PaymentsController extends Controller
{
    public function index()
    {

        abort_unless(auth()->user()->can('view payment'), 403);

        if (\request()->ajax()) {
            $payments = Payment::with(['tenant', 'invoice.property', 'invoice.house'])->latest();
            return DataTables::of($payments)
                ->filter(function ($query) {
                    //if both from_date and to_date are filled, then we need to query between the two dates
                    if (request()->filled('from_date') && request()->filled('to_date')) {
                        $query->whereBetween('paid_at', [request()->get('from_date'), request()->get('to_date')]);
                    }

                    //if status is filled, then we need to query by status
                    if (request()->filled('status')) {
                        $query->where('status', request()->get('status'));
                    }

                }, true)
                ->addIndexColumn()
                ->addColumn('tenant', function ($payment) {
                    return $payment->tenant?->name ?? 'N/A';
                })
                ->addColumn('actions', function ($payment) {
                    return '';
                })
                ->addColumn('property', function ($payment) {
                    return $payment->invoice?->property?->name;
                })
                ->addColumn('house', function ($payment) {
                    return $payment->invoice?->house?->name;
                })
                ->editColumn('paid_at', function ($payment) {
                    return $payment->paid_at?->format('d M Y');
                })
                ->editColumn('amount', function ($payment) {
                    return setting('currency_symbol') . ' ' . number_format($payment->amount, 2);
                })
                ->editColumn('reference_number', function ($payment) {
                    return view('admin.payments.partials.payment_reference', compact('payment'))->render();
                })
                ->editColumn('status', function ($payment) {
                    return view('admin.payments.partials.status', compact('payment'))->render();
                })
                ->addColumn('actions', function ($payment) {
                    return view('admin.payments.partials.actions', compact('payment'))->render();
                })
                ->rawColumns(['reference_number', 'status', 'actions'])
                ->make(true);
        }
        return view('admin.payments.index');

    }


}
