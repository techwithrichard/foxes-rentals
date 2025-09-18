<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\User;
use App\Services\PaymentService;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Enums\PaymentStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PaymentConsolidatedController extends Controller
{
    protected $paymentService;
    protected $paymentRepository;

    public function __construct(
        PaymentService $paymentService,
        PaymentRepositoryInterface $paymentRepository
    ) {
        $this->paymentService = $paymentService;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * Display a listing of payments
     */
    public function index(Request $request): View
    {
        $this->authorize('view payment');

        $filters = $request->only([
            'status', 'payment_method', 'tenant_id', 'landlord_id', 'property_id',
            'invoice_id', 'start_date', 'end_date', 'min_amount', 'max_amount',
            'verified', 'has_receipt', 'reference', 'notes'
        ]);

        $payments = $this->paymentRepository->searchPayments($filters);
        $tenants = User::role('tenant')->get();
        $landlords = User::role('landlord')->get();
        $paymentMethods = $this->paymentService->getAvailablePaymentMethods();

        return view('admin.payments-consolidated.index', compact(
            'payments', 'tenants', 'landlords', 'paymentMethods'
        ));
    }

    /**
     * Show the form for creating a new payment
     */
    public function create(Request $request): View
    {
        $this->authorize('create payment');

        $invoiceId = $request->get('invoice_id');
        $invoice = $invoiceId ? Invoice::find($invoiceId) : null;
        $invoices = Invoice::with(['tenant', 'property'])->get();
        $paymentMethods = $this->paymentService->getAvailablePaymentMethods();

        return view('admin.payments-consolidated.create', compact(
            'invoice', 'invoices', 'paymentMethods'
        ));
    }

    /**
     * Store a newly created payment
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create payment');

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'invoice_id' => 'required|exists:invoices,id',
            'payment_method' => 'required|string',
            'paid_at' => 'nullable|date',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'payment_receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'phone' => 'nullable|string', // For MPesa payments
        ]);

        // Validate payment data
        $errors = $this->paymentService->validatePaymentData($validated);
        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors
            ], 422);
        }

        try {
            $result = $this->paymentService->createPayment($validated);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment created successfully.',
                    'data' => $result['payment'],
                    'gateway_response' => $result['gateway_response']
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified payment
     */
    public function show(Payment $payment): View
    {
        $this->authorize('view payment');

        $payment->load(['invoice', 'tenant', 'property', 'house', 'recordedBy', 'verifiedBy']);

        return view('admin.payments-consolidated.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment
     */
    public function edit(Payment $payment): View
    {
        $this->authorize('edit payment');

        $payment->load(['invoice', 'tenant', 'property']);
        $paymentMethods = $this->paymentService->getAvailablePaymentMethods();

        return view('admin.payments-consolidated.edit', compact('payment', 'paymentMethods'));
    }

    /**
     * Update the specified payment
     */
    public function update(Request $request, Payment $payment): JsonResponse
    {
        $this->authorize('edit payment');

        $validated = $request->validate([
            'amount' => 'sometimes|numeric|min:0.01',
            'payment_method' => 'sometimes|string',
            'paid_at' => 'nullable|date',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'payment_receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status' => 'sometimes|in:' . implode(',', array_column(PaymentStatusEnum::cases(), 'value')),
        ]);

        try {
            $payment->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Payment updated successfully.',
                'data' => $payment->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified payment
     */
    public function destroy(Payment $payment): JsonResponse
    {
        $this->authorize('delete payment');

        try {
            $result = $this->paymentService->cancelPayment($payment);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment cancelled successfully.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify payment
     */
    public function verify(Payment $payment): JsonResponse
    {
        $this->authorize('verify payment');

        try {
            $result = $this->paymentService->verifyPayment($payment);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment verified successfully.',
                    'data' => $payment->fresh()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process payment
     */
    public function process(Payment $payment): JsonResponse
    {
        $this->authorize('process payment');

        try {
            $result = $this->paymentService->processPayment($payment);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Payment processed successfully.' : $result['error'],
                'data' => $payment->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refund payment
     */
    public function refund(Request $request, Payment $payment): JsonResponse
    {
        $this->authorize('refund payment');

        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0.01|max:' . $payment->amount,
            'reason' => 'nullable|string'
        ]);

        try {
            $result = $this->paymentService->refundPayment($payment, $validated['amount'] ?? null);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment refunded successfully.',
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refund payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment status
     */
    public function status(Payment $payment): JsonResponse
    {
        $this->authorize('view payment');

        try {
            $result = $this->paymentService->getPaymentStatus($payment);

            return response()->json([
                'success' => $result['success'],
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get payment status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment statistics
     */
    public function statistics(): JsonResponse
    {
        $this->authorize('view payment');

        try {
            $statistics = $this->paymentService->getPaymentStatistics();

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available payment methods
     */
    public function paymentMethods(): JsonResponse
    {
        try {
            $methods = $this->paymentService->getAvailablePaymentMethods();
            $gateways = $this->paymentService->getAvailableGateways();

            return response()->json([
                'success' => true,
                'data' => [
                    'methods' => $methods,
                    'gateways' => $gateways
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get payment methods: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payments by status
     */
    public function getByStatus(string $status): JsonResponse
    {
        $this->authorize('view payment');

        try {
            $payments = $this->paymentRepository->findByStatus(PaymentStatusEnum::from($status));

            return response()->json([
                'success' => true,
                'data' => $payments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get payments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payments by method
     */
    public function getByMethod(string $method): JsonResponse
    {
        $this->authorize('view payment');

        try {
            $payments = $this->paymentRepository->findByPaymentMethod($method);

            return response()->json([
                'success' => true,
                'data' => $payments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get payments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent payments
     */
    public function recent(): JsonResponse
    {
        $this->authorize('view payment');

        try {
            $payments = $this->paymentRepository->getRecent(20);

            return response()->json([
                'success' => true,
                'data' => $payments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recent payments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payments requiring attention
     */
    public function requiringAttention(): JsonResponse
    {
        $this->authorize('view payment');

        try {
            $payments = $this->paymentRepository->getRequiringAttention();

            return response()->json([
                'success' => true,
                'data' => $payments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get payments requiring attention: ' . $e->getMessage()
            ], 500);
        }
    }
}
