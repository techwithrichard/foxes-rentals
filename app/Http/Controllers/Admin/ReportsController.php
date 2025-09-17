<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Lease;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ReportsController extends Controller
{
    public function landlordIncome()
    {
        abort_unless(auth()->user()->can('view landlord income report'), 403);
        return view('admin.reports.landlord-income');

    }

    public function propertyIncome()
    {
        abort_unless(auth()->user()->can('view property income report'), 403);
        return view('admin.reports.property-income');

    }

    public function companyIncome()
    {
        abort_unless(auth()->user()->can('view company income report'), 403);
        return view('admin.reports.company-income');
    }

    public function outstandingPayments()
    {
        abort_unless(auth()->user()->can('view outstanding payments report'), 403);
        if (\request()->ajax()) {
            $invoices = Invoice::query()
                ->with(['tenant:id,name', 'property:id,name', 'house:id,name'])
                ->whereIn('status', [PaymentStatusEnum::PARTIALLY_PAID, PaymentStatusEnum::PENDING, PaymentStatusEnum::OVERDUE])
                ->latest('id')
                ->select('invoices.*')
                ->latest();

            return DataTables::of($invoices)
                ->filter(function ($query) {
                    //if request has both date_from and date_to filled,filter by between dates
                    if (request()->filled('date_from') && request()->filled('date_to')) {
                        $query->whereBetween('created_at', [request()->get('date_from'), request()->get('date_to')]);
                    }


                }, true)
                ->addIndexColumn()
                ->editColumn('invoice_id', function ($invoice) {
                    return view('admin.reports.partials.invoice', compact('invoice'))->render();
                })
                ->addColumn('actions', function ($invoice) {
                    return view('admin.invoice.rent.partials.actions', compact('invoice'));
                })
                ->editColumn('created_at', function ($invoice) {
                    return $invoice->created_at?->format('d M, Y');
                })
                ->addColumn('balance_due', function ($invoice) {
                    return @setting('currency_symbol') . ' ' . number_format($invoice->amount + $invoice->bills_amount, 2);
                })
                ->editColumn('status', function ($invoice) {
                    return view('admin.invoice.rent.partials.status', compact('invoice'));
                })
                ->addColumn('property', function ($invoice) {
                    return $invoice->property?->name ?? '';
                })
                ->addColumn('house', function ($invoice) {
                    return $invoice->house?->name ?? '';
                })
                ->addColumn('balance', function ($invoice) {
                    return @setting('currency_symbol') . ' '
                        . number_format($invoice->balance_due, 2);
                })
                ->addColumn('tenant', function ($invoice) {
                    return $invoice->tenant?->name ?? 'Archived Tenant';
                })
                ->rawColumns(['actions', 'status', 'invoice_id'])
//                ->setRowClass('nk-tb-item')
                ->make(true);

        }

        return view('admin.reports.outstanding-payments');
    }

    public function landlordExpenses()
    {
        abort_unless(auth()->user()->can('view landlord expenses report'), 403);
        return view('admin.reports.landlord-expenses');

    }

    public function companyExpenses()
    {
        abort_unless(auth()->user()->can('view company expenses report'), 403);
        return view('admin.reports.company-expenses');
    }

    public function expiringLeases()
    {
        abort_unless(auth()->user()->can('view expiring leases report'), 403);
        if (\request()->ajax()) {
            $leases = Lease::query()
                ->with('tenant:id,name', 'property:id,name', 'house:id,name')
                ->select('leases.*')
                ->withSum('bills', 'amount')
                ->latest('id');

            return DataTables::of($leases)
                ->filter(function ($query) {

                    if (request()->filled('days_left')) {
                        $query->where('end_date', '<=', now()->addDays(request()->get('days_left')));
                    } else {
                        $query->where('end_date', '<=', now()->addDays(7));
                    }
                }, true)
                ->addIndexColumn()
                ->addColumn('actions', function ($lease) {
                    return view('admin.lease.partials.actions', compact('lease'));
                })
                ->addColumn('tenant', function ($lease) {
                    return $lease->tenant?->name;
                })
                ->addColumn('property', function ($lease) {
                    return $lease->property?->name;
                })
                ->addColumn('house', function ($lease) {
                    return $lease->house?->name;
                })
                ->editColumn('rent_cycle', function ($lease) {
//                    return $lease->rent_cycle . ' ' . ($lease->rent_cycle > 1 ? __('months') : __('month'));
                    return $lease->rent_cycle . ' ' . __($lease->rent_cycle > 1 ? 'months' : 'month');
                })
                ->editColumn('start_date', function ($lease) {
                    return $lease->start_date?->format('d M, Y');
                })
                ->editColumn('end_date', function ($lease) {
                    return $lease->end_date?->format('d M, Y');
                })
                ->rawColumns(['actions', 'rent_and_bills', 'lease_dates'])
                ->toJson();
        }

        return view('admin.reports.expiring-leases');

    }

    public function maintenance()
    {
        abort_unless(auth()->user()->can('view maintenance reports'), 403);
        
        // For now, redirect to the maintenance requests page
        // You can implement a dedicated maintenance reports view later
        return redirect()->route('admin.maintenance.requests');
    }

    public function occupancy()
    {
        abort_unless(auth()->user()->can('view occupancy reports'), 403);
        
        // For now, redirect to the properties page
        // You can implement a dedicated occupancy reports view later
        return redirect()->route('admin.properties.index');
    }
}
