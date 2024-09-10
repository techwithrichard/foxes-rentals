<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Lease;
use App\Services\MPesaHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        //get tenant leases which are not soft deleted
        $activeLeases = Lease::with(['property', 'house'])
            ->withSum('bills', 'amount')
            ->where('tenant_id', auth()->user()->id)
            ->get();

        $archivedLeases = Lease::with(['property', 'house'])
            ->withSum('bills', 'amount')
            ->where('tenant_id', auth()->user()->id)
            ->onlyTrashed()
            ->get();

        // get total tenant balances from all invoices


        $outstanding_balances = Invoice::select('id', 'tenant_id', 'amount', 'bills_amount', 'paid_amount')
            ->selectRaw('amount+bills_amount-paid_amount AS outstanding_balances')
            ->where('tenant_id', auth()->user()->id)
            ->get()
            ->sum('outstanding_balances');

        $outstanding_balances = Invoice::where('tenant_id', auth()->user()->id)
            ->unpaidStatus()
            ->sum(DB::raw('amount + bills_amount - paid_amount'));

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $outstanding_balances_due_this_month = Invoice::where('tenant_id', auth()->user()->id)
            ->unpaidStatus()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum(DB::raw('amount + bills_amount - paid_amount'));

        return view('tenant.home.index',
            compact('activeLeases', 'archivedLeases', 'outstanding_balances', 'outstanding_balances_due_this_month')
        );

    }

    public function settings()
    {
        return view('tenant.home.settings');
    }

    public function notifications()
    {
        return view('tenant.home.notifications');
    }

    public function initiateMpesaPayment($invoiceId)
    {

        $invoice = Invoice::query()
            ->with(['tenant'])
            ->findOrFail($invoiceId);

        $amount = $invoice->balance_due;
        $phone = $invoice->tenant?->phone;
        $reference = $invoice->lease_reference ?? 'Invoice-' . $invoice->invoice_id;

        $response = MPesaHelper::stkPush($phone, $amount, $reference);


        if ($response['status'] == 'success') {
            return view('tenant.payments.mpesa_success', compact('amount', 'reference'));
        }


        $errorMessage = $response['errorMessage'] ?? 'Failed to initiate payment';

        return view('tenant.payments.mpesa_error', compact('errorMessage', 'amount', 'reference'));


    }
}
