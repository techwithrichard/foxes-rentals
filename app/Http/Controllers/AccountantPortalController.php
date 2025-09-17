<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Property;

class AccountantPortalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:accountant']);
    }

    /**
     * Display the accountant dashboard
     */
    public function index()
    {
        // Get financial statistics
        $stats = [
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->sum('amount'),
            'total_expenses' => Expense::sum('amount'),
            'monthly_revenue' => Payment::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'monthly_expenses' => Expense::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];

        // Get recent transactions
        $recentPayments = Payment::with(['lease.property', 'lease.tenant'])
            ->latest()
            ->limit(10)
            ->get();

        $recentExpenses = Expense::latest()->limit(10)->get();

        return view('portals.accountant.dashboard', compact('stats', 'recentPayments', 'recentExpenses'));
    }

    /**
     * Display all payments
     */
    public function payments()
    {
        $payments = Payment::with(['lease.property', 'lease.tenant'])
            ->latest()
            ->paginate(20);

        return view('portals.accountant.payments', compact('payments'));
    }

    /**
     * Display all invoices
     */
    public function invoices()
    {
        $invoices = Invoice::with(['lease.property', 'lease.tenant'])
            ->latest()
            ->paginate(20);

        return view('portals.accountant.invoices', compact('invoices'));
    }

    /**
     * Display all expenses
     */
    public function expenses()
    {
        $expenses = Expense::latest()->paginate(20);

        return view('portals.accountant.expenses', compact('expenses'));
    }

    /**
     * Display financial reports
     */
    public function reports()
    {
        // Monthly revenue for the last 12 months
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenue = Payment::where('status', 'completed')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('amount');
            
            $monthlyRevenue[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue
            ];
        }

        // Property-wise revenue
        $propertyRevenue = Property::withSum('payments', 'amount')
            ->orderBy('payments_sum_amount', 'desc')
            ->limit(10)
            ->get();

        return view('portals.accountant.reports', compact('monthlyRevenue', 'propertyRevenue'));
    }

    /**
     * Display payment reconciliation
     */
    public function reconciliation()
    {
        $unreconciledPayments = Payment::where('status', 'pending')
            ->with(['lease.property', 'lease.tenant'])
            ->latest()
            ->get();

        return view('portals.accountant.reconciliation', compact('unreconciledPayments'));
    }

    /**
     * Reconcile a payment
     */
    public function reconcile(Request $request, Payment $payment)
    {
        $request->validate([
            'status' => 'required|in:completed,failed',
            'notes' => 'nullable|string|max:500',
        ]);

        $payment->update([
            'status' => $request->status,
            'reconciled_at' => now(),
            'reconciled_by' => auth()->id(),
            'notes' => $request->notes,
        ]);

        return redirect()->back()
            ->with('success', 'Payment reconciled successfully.');
    }

    /**
     * Display accountant's profile
     */
    public function profile()
    {
        $user = auth()->user();
        return view('portals.accountant.profile', compact('user'));
    }

    /**
     * Update accountant's profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'certification' => 'nullable|string|max:255',
        ]);

        $user->update($request->only(['name', 'phone', 'certification']));

        return redirect()->route('accountant.profile')
            ->with('success', 'Profile updated successfully.');
    }
}
