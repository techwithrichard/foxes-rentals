<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PaymentsController extends Controller
{
    public function index()
    {
        if (\request()->ajax()) {
            $payments = Payment::with(['invoice.property', 'invoice.house'])
                ->where('tenant_id', auth()->id())
                ->latest();
            return DataTables::of($payments)
                ->addIndexColumn()
                ->addColumn('property', function ($payment) {
                    return $payment->invoice->property->name;
                })
                ->addColumn('house', function ($payment) {
                    return $payment->invoice->house->name;
                })
                ->editColumn('paid_at', function ($payment) {
                    return $payment->paid_at->format('d M Y');
                })
                ->editColumn('amount', function ($payment) {
                    return setting('currency_symbol') . ' ' . number_format($payment->amount, 2);
                })
                ->editColumn('status', function ($payment) {
                    return view('admin.payments.partials.status', compact('payment'));
                })
                ->setRowClass('nk-tb-item')
                ->rawColumns(['status'])
                ->make(true);
        }

        return view('tenant.payments.index');
    }


}
