<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\StkRequest;
use App\Models\C2bRequest;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Overpayment;
use App\Enums\PaymentStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PaymentVerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view payment');
    }

    /**
     * Display the payment verification dashboard
     */
    public function index()
    {
        return view('admin.payment-verification.index');
    }

    /**
     * Show unverified payments (payments that went through but weren't automatically processed)
     */
    public function unverifiedPayments()
    {
        if (request()->ajax()) {
            $unverifiedPayments = $this->getUnverifiedPayments();
            
            return DataTables::of($unverifiedPayments)
                ->addIndexColumn()
                ->addColumn('tenant_name', function ($payment) {
                    return $payment->tenant?->name ?? 'Unknown';
                })
                ->addColumn('tenant_phone', function ($payment) {
                    return $payment->tenant?->phone ?? 'N/A';
                })
                ->addColumn('property_name', function ($payment) {
                    return $payment->invoice?->property?->name ?? 'N/A';
                })
                ->addColumn('house_name', function ($payment) {
                    return $payment->invoice?->house?->name ?? 'N/A';
                })
                ->addColumn('invoice_amount', function ($payment) {
                    return $payment->invoice ? 
                        setting('currency_symbol') . ' ' . number_format($payment->invoice->amount, 2) : 
                        'N/A';
                })
                ->addColumn('invoice_balance', function ($payment) {
                    if ($payment->invoice) {
                        $balance = $payment->invoice->amount - $payment->invoice->paid_amount;
                        return setting('currency_symbol') . ' ' . number_format($balance, 2);
                    }
                    return 'N/A';
                })
                ->addColumn('verification_status', function ($payment) {
                    return view('admin.payment-verification.partials.verification_status', compact('payment'));
                })
                ->addColumn('actions', function ($payment) {
                    return view('admin.payment-verification.partials.actions', compact('payment'));
                })
                ->editColumn('amount', function ($payment) {
                    return setting('currency_symbol') . ' ' . number_format($payment->amount, 2);
                })
                ->editColumn('paid_at', function ($payment) {
                    return $payment->paid_at?->format('d M Y H:i');
                })
                ->rawColumns(['verification_status', 'actions'])
                ->make(true);
        }

        return view('admin.payment-verification.unverified');
    }

    /**
     * Show manual payment entry form
     */
    public function create()
    {
        $tenants = User::role('tenant')->select('id', 'name', 'phone')->get();
        $invoices = Invoice::with(['property', 'house', 'tenant'])
            ->where('status', '!=', PaymentStatusEnum::PAID)
            ->get();
            
        return view('admin.payment-verification.create', compact('tenants', 'invoices'));
    }

    /**
     * Store manually entered payment
     */
    public function store(Request $request)
    {
        $request->validate([
            'reference_number' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'tenant_id' => 'required|exists:users,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'payment_method' => 'required|string',
            'paid_at' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $payment = Payment::create([
                'amount' => $request->amount,
                'paid_at' => $request->paid_at,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'tenant_id' => $request->tenant_id,
                'invoice_id' => $request->invoice_id,
                'recorded_by' => auth()->id(),
                'landlord_id' => $request->invoice_id ? Invoice::find($request->invoice_id)->landlord_id : null,
                'commission' => $request->invoice_id ? Invoice::find($request->invoice_id)->commission : 0,
                'property_id' => $request->invoice_id ? Invoice::find($request->invoice_id)->property_id : null,
                'house_id' => $request->invoice_id ? Invoice::find($request->invoice_id)->house_id : null,
                'status' => PaymentStatusEnum::PAID,
                'notes' => $request->notes,
            ]);

            // If invoice is specified, reconcile the payment
            if ($request->invoice_id) {
                $invoice = Invoice::find($request->invoice_id);
                $invoice->pay($request->amount);
                
                // Check for overpayment
                $totalAmount = $invoice->amount + $invoice->bills_amount;
                if ($invoice->paid_amount > $totalAmount) {
                    $overpaymentAmount = $invoice->paid_amount - $totalAmount;
                    Overpayment::create([
                        'tenant_id' => $request->tenant_id,
                        'amount' => $overpaymentAmount,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.payment-verification.index')
                ->with('success', 'Payment recorded successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    /**
     * Search for M-PESA transaction by reference number
     */
    public function searchTransaction(Request $request)
    {
        $request->validate([
            'reference_number' => 'required|string',
        ]);

        $reference = $request->reference_number;
        
        // Search in STK requests
        $stkRequest = StkRequest::where('MpesaReceiptNumber', $reference)->first();
        
        // Search in C2B requests
        $c2bRequest = C2bRequest::where('TransID', $reference)->first();
        
        // Search in existing payments
        $existingPayment = Payment::where('reference_number', $reference)->first();

        $results = [
            'reference' => $reference,
            'stk_request' => $stkRequest,
            'c2b_request' => $c2bRequest,
            'existing_payment' => $existingPayment,
            'found' => $stkRequest || $c2bRequest || $existingPayment,
        ];

        return response()->json($results);
    }

    /**
     * Verify and reconcile a payment
     */
    public function verifyPayment(Request $request, $id)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $payment = Payment::findOrFail($id);
            $invoice = Invoice::findOrFail($request->invoice_id);

            // Update payment with invoice information
            $payment->update([
                'invoice_id' => $request->invoice_id,
                'landlord_id' => $invoice->landlord_id,
                'commission' => $invoice->commission,
                'property_id' => $invoice->property_id,
                'house_id' => $invoice->house_id,
                'notes' => $request->notes,
                'verified_at' => now(),
                'verified_by' => auth()->id(),
            ]);

            // Reconcile with invoice
            $invoice->pay($payment->amount);

            // Check for overpayment
            $totalAmount = $invoice->amount + $invoice->bills_amount;
            if ($invoice->paid_amount > $totalAmount) {
                $overpaymentAmount = $invoice->paid_amount - $totalAmount;
                Overpayment::create([
                    'tenant_id' => $payment->tenant_id,
                    'amount' => $overpaymentAmount,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment verified and reconciled successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error verifying payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get unverified payments (payments without invoice association)
     */
    private function getUnverifiedPayments()
    {
        return Payment::with(['tenant', 'invoice.property', 'invoice.house'])
            ->whereNull('invoice_id')
            ->orWhere('verified_at', null)
            ->latest()
            ->get();
    }

    /**
     * Get tenant invoices for payment verification
     */
    public function getTenantInvoices(Request $request)
    {
        $tenantId = $request->tenant_id;
        
        $invoices = Invoice::with(['property', 'house'])
            ->where('tenant_id', $tenantId)
            ->where('status', '!=', PaymentStatusEnum::PAID)
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_id' => $invoice->invoice_id,
                    'amount' => $invoice->amount,
                    'paid_amount' => $invoice->paid_amount,
                    'balance' => $invoice->amount - $invoice->paid_amount,
                    'property_name' => $invoice->property?->name,
                    'house_name' => $invoice->house?->name,
                    'created_at' => $invoice->created_at->format('d M Y'),
                ];
            });

        return response()->json($invoices);
    }

    /**
     * Show payment verification statistics
     */
    public function statistics()
    {
        $stats = [
            'total_payments' => Payment::count(),
            'verified_payments' => Payment::whereNotNull('verified_at')->count(),
            'unverified_payments' => Payment::whereNull('verified_at')->count(),
            'pending_stk_requests' => StkRequest::where('status', 'Request Sent')->count(),
            'failed_stk_requests' => StkRequest::where('status', 'Failed')->count(),
            'total_overpayments' => Overpayment::sum('amount'),
        ];

        return response()->json($stats);
    }
}

