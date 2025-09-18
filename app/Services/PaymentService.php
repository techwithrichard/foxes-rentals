<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\User;
use App\Services\PaymentGateways\PaymentGatewayManager;
use App\Enums\PaymentStatusEnum;
use App\Events\InvoicePaidEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $gatewayManager;

    public function __construct(PaymentGatewayManager $gatewayManager)
    {
        $this->gatewayManager = $gatewayManager;
    }

    /**
     * Create a new payment with gateway integration
     */
    public function createPayment(array $paymentData): array
    {
        try {
            DB::beginTransaction();

            // Get fresh invoice data
            $invoice = Invoice::with(['tenant', 'property', 'house'])->findOrFail($paymentData['invoice_id']);
            $invoice->refresh();

            Log::info("Creating payment for invoice {$invoice->invoice_id}", $paymentData);

            // Calculate commission and landlord info
            $commission = $invoice->commission;
            $landlordId = $invoice->landlord_id;
            $propertyId = $invoice->property_id;
            $houseId = $invoice->house_id;

            // Initialize payment with gateway
            $gatewayResponse = $this->gatewayManager->initializePayment($paymentData);
            
            if (!$gatewayResponse['success']) {
                throw new \Exception($gatewayResponse['error']);
            }

            // Create payment record
            $payment = Payment::create([
                'amount' => $paymentData['amount'],
                'paid_at' => $paymentData['paid_at'] ?? now(),
                'payment_method' => $paymentData['payment_method'],
                'reference_number' => $gatewayResponse['reference'],
                'tenant_id' => $invoice->tenant_id,
                'invoice_id' => $invoice->id,
                'recorded_by' => $paymentData['recorded_by'] ?? auth()->id(),
                'landlord_id' => $landlordId,
                'commission' => $commission,
                'property_id' => $propertyId,
                'house_id' => $houseId,
                'status' => $paymentData['status'] ?? PaymentStatusEnum::PENDING,
                'verified_at' => $paymentData['verified_at'] ?? null,
                'verified_by' => $paymentData['verified_by'] ?? null,
                'notes' => $paymentData['notes'] ?? null,
                'payment_receipt' => $paymentData['payment_receipt'] ?? null,
                'gateway_data' => $gatewayResponse['data'] ?? null,
            ]);

            // Process payment if it's immediate (like cash)
            if ($payment->status === PaymentStatusEnum::PAID) {
                $this->applyPaymentToInvoice($invoice, $payment);
            }

            DB::commit();

            Log::info("Payment created successfully: {$payment->id}");

            return [
                'success' => true,
                'payment' => $payment->load(['invoice', 'tenant', 'property']),
                'gateway_response' => $gatewayResponse
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error creating payment: " . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process payment with gateway
     */
    public function processPayment(Payment $payment): array
    {
        try {
            $gatewayResponse = $this->gatewayManager->processPayment($payment);
            
            if ($gatewayResponse['success']) {
                // Update payment status if needed
                if (isset($gatewayResponse['status'])) {
                    $payment->update(['status' => PaymentStatusEnum::PAID]);
                    $this->applyPaymentToInvoice($payment->invoice, $payment);
                }
            }

            return $gatewayResponse;

        } catch (\Exception $e) {
            Log::error("Error processing payment: " . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify payment with gateway
     */
    public function verifyPayment(Payment $payment): array
    {
        try {
            $gatewayResponse = $this->gatewayManager->verifyPayment(
                $payment->reference_number, 
                $payment->payment_method
            );
            
            if ($gatewayResponse['success']) {
                // Update payment with verification data
                $payment->update([
                    'status' => PaymentStatusEnum::PAID,
                    'verified_at' => now(),
                    'verified_by' => auth()->id()
                ]);
                
                $this->applyPaymentToInvoice($payment->invoice, $payment);
            }

            return $gatewayResponse;

        } catch (\Exception $e) {
            Log::error("Error verifying payment: " . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Cancel payment
     */
    public function cancelPayment(Payment $payment): array
    {
        try {
            DB::beginTransaction();

            $gatewayResponse = $this->gatewayManager->cancelPayment($payment);
            
            if ($gatewayResponse['success']) {
                $payment->update(['status' => PaymentStatusEnum::CANCELLED]);
                
                // Reverse payment from invoice if it was applied
                if ($payment->status === PaymentStatusEnum::PAID) {
                    $this->reversePaymentFromInvoice($payment->invoice, $payment);
                }
            }

            DB::commit();
            return $gatewayResponse;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error cancelling payment: " . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Refund payment
     */
    public function refundPayment(Payment $payment, float $amount = null): array
    {
        try {
            DB::beginTransaction();

            $gatewayResponse = $this->gatewayManager->refundPayment($payment, $amount);
            
            if ($gatewayResponse['success']) {
                // Create refund record
                $refund = Payment::create([
                    'amount' => -($amount ?? $payment->amount),
                    'payment_method' => $payment->payment_method . '_REFUND',
                    'reference_number' => 'REFUND_' . $payment->reference_number,
                    'tenant_id' => $payment->tenant_id,
                    'invoice_id' => $payment->invoice_id,
                    'status' => PaymentStatusEnum::PAID,
                    'notes' => "Refund for payment {$payment->reference_number}",
                    'parent_payment_id' => $payment->id,
                    'verified_at' => now(),
                    'verified_by' => auth()->id()
                ]);

                // Apply refund to invoice
                $this->applyPaymentToInvoice($payment->invoice, $refund);
            }

            DB::commit();
            return $gatewayResponse;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error refunding payment: " . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get payment status from gateway
     */
    public function getPaymentStatus(Payment $payment): array
    {
        try {
            return $this->gatewayManager->getPaymentStatus(
                $payment->reference_number, 
                $payment->payment_method
            );

        } catch (\Exception $e) {
            Log::error("Error getting payment status: " . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get available payment methods
     */
    public function getAvailablePaymentMethods(): array
    {
        return $this->gatewayManager->getAllSupportedMethods();
    }

    /**
     * Get available gateways
     */
    public function getAvailableGateways(): array
    {
        return $this->gatewayManager->getAvailableGateways();
    }

    /**
     * Validate payment data
     */
    public function validatePaymentData(array $data): array
    {
        $errors = [];

        if (!isset($data['amount']) || $data['amount'] <= 0) {
            $errors[] = 'Amount must be greater than 0';
        }

        if (!isset($data['invoice_id'])) {
            $errors[] = 'Invoice ID is required';
        } else {
            $invoice = Invoice::find($data['invoice_id']);
            if (!$invoice) {
                $errors[] = 'Invoice not found';
            }
        }

        if (!isset($data['payment_method']) || empty($data['payment_method'])) {
            $errors[] = 'Payment method is required';
        } elseif (!$this->gatewayManager->isMethodSupported($data['payment_method'])) {
            $errors[] = 'Payment method not supported';
        }

        return $errors;
    }

    /**
     * Apply payment to invoice
     */
    protected function applyPaymentToInvoice(Invoice $invoice, Payment $payment): void
    {
        $invoice->refresh();
        $invoice->pay($payment->amount);
        InvoicePaidEvent::dispatch($invoice);
        
        Log::info("Payment applied to invoice {$invoice->invoice_id}");
    }

    /**
     * Reverse payment from invoice
     */
    protected function reversePaymentFromInvoice(Invoice $invoice, Payment $payment): void
    {
        $invoice->refresh();
        $invoice->paid_amount -= $payment->amount;
        $invoice->updateStatus();
        $invoice->save();
        
        Log::info("Payment reversed from invoice {$invoice->invoice_id}");
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStatistics(): array
    {
        $totalPayments = Payment::count();
        $totalAmount = Payment::where('status', PaymentStatusEnum::PAID)->sum('amount');
        $pendingPayments = Payment::where('status', PaymentStatusEnum::PENDING)->count();
        $cancelledPayments = Payment::where('status', PaymentStatusEnum::CANCELLED)->count();

        $methods = Payment::selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
            ->where('status', PaymentStatusEnum::PAID)
            ->groupBy('payment_method')
            ->get();

        return [
            'total_payments' => $totalPayments,
            'total_amount' => $totalAmount,
            'pending_payments' => $pendingPayments,
            'cancelled_payments' => $cancelledPayments,
            'payment_methods' => $methods,
            'available_gateways' => $this->getAvailableGateways()
        ];
    }

    /**
     * Handle STK Push callback
     */
    public function handleStkCallback(array $callbackData): array
    {
        try {
            $mpesaGateway = $this->gatewayManager->getGateway('mpesa');
            if (!$mpesaGateway) {
                return [
                    'success' => false,
                    'error' => 'MPesa gateway not available'
                ];
            }

            return $mpesaGateway->handleStkCallback($callbackData);

        } catch (\Exception $e) {
            Log::error("Error handling STK callback: " . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
