<?php

namespace App\Services\PaymentGateways;

use App\Models\Payment;
use App\Services\PaymentGateways\Contracts\PaymentGatewayInterface;
use App\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\Log;

class CashGateway implements PaymentGatewayInterface
{
    protected $config;

    public function __construct()
    {
        $this->config = config('cash_payment', []);
    }

    /**
     * Initialize cash payment
     */
    public function initializePayment(array $paymentData): array
    {
        try {
            Log::info('Initializing cash payment', $paymentData);

            // Generate reference number
            $reference = $this->generateReference($paymentData);

            return [
                'success' => true,
                'gateway' => 'cash',
                'reference' => $reference,
                'message' => 'Cash payment initialized',
                'data' => [
                    'reference' => $reference,
                    'amount' => $paymentData['amount'],
                    'payment_location' => $this->config['payment_location'] ?? 'Office',
                    'accepted_by' => $this->config['accepted_by'] ?? 'Cashier'
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Cash payment initialization failed', [
                'error' => $e->getMessage(),
                'payment_data' => $paymentData
            ]);

            return [
                'success' => false,
                'gateway' => 'cash',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process cash payment
     */
    public function processPayment(Payment $payment): array
    {
        try {
            // Cash payments are immediately processed
            $payment->update([
                'status' => PaymentStatusEnum::PAID,
                'verified_at' => now(),
                'verified_by' => auth()->id()
            ]);

            return [
                'success' => true,
                'status' => 'completed',
                'message' => 'Cash payment processed successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Cash payment processing failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify cash payment
     */
    public function verifyPayment(string $reference): array
    {
        try {
            $payment = Payment::where('reference_number', $reference)->first();
            
            if (!$payment) {
                return [
                    'success' => false,
                    'error' => 'Payment not found'
                ];
            }

            return [
                'success' => true,
                'status' => $payment->status->value,
                'amount' => $payment->amount,
                'reference' => $payment->reference_number,
                'verified_at' => $payment->verified_at,
                'verified_by' => $payment->verified_by,
                'data' => $payment->toArray()
            ];

        } catch (\Exception $e) {
            Log::error('Cash payment verification failed', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Cancel cash payment
     */
    public function cancelPayment(Payment $payment): array
    {
        try {
            $payment->update(['status' => PaymentStatusEnum::CANCELLED]);

            return [
                'success' => true,
                'message' => 'Cash payment cancelled successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Cash payment cancellation failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Refund cash payment
     */
    public function refundPayment(Payment $payment, float $amount = null): array
    {
        try {
            $refundAmount = $amount ?? $payment->amount;
            
            // Create refund record
            $refund = Payment::create([
                'amount' => -$refundAmount, // Negative amount for refund
                'payment_method' => 'CASH_REFUND',
                'reference_number' => 'REFUND_' . $payment->reference_number,
                'tenant_id' => $payment->tenant_id,
                'invoice_id' => $payment->invoice_id,
                'status' => PaymentStatusEnum::PAID,
                'notes' => "Cash refund for payment {$payment->reference_number}",
                'parent_payment_id' => $payment->id,
                'verified_at' => now(),
                'verified_by' => auth()->id()
            ]);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'amount' => $refundAmount,
                'message' => 'Cash refund processed successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Cash refund failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $reference): array
    {
        return $this->verifyPayment($reference);
    }

    /**
     * Get supported payment methods
     */
    public function getSupportedMethods(): array
    {
        return [
            'CASH' => 'Cash Payment',
            'CASH_OFFICE' => 'Cash at Office',
            'CASH_COLLECTION' => 'Cash Collection'
        ];
    }

    /**
     * Check if gateway is available
     */
    public function isAvailable(): bool
    {
        return true; // Cash payments are always available
    }

    /**
     * Get gateway configuration
     */
    public function getConfig(): array
    {
        return [
            'gateway' => 'cash',
            'payment_location' => $this->config['payment_location'] ?? 'Office',
            'accepted_by' => $this->config['accepted_by'] ?? 'Cashier',
            'available' => $this->isAvailable()
        ];
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
        }

        return $errors;
    }

    /**
     * Generate reference number for cash payment
     */
    protected function generateReference(array $paymentData): string
    {
        $invoiceId = $paymentData['invoice_id'] ?? 'UNKNOWN';
        $timestamp = now()->format('YmdHis');
        $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        
        return "CASH{$invoiceId}{$timestamp}{$random}";
    }

    /**
     * Get cash payment receipt
     */
    public function generateReceipt(Payment $payment): array
    {
        return [
            'receipt_number' => $payment->reference_number,
            'amount' => $payment->amount,
            'payment_method' => $payment->payment_method,
            'paid_at' => $payment->paid_at,
            'verified_at' => $payment->verified_at,
            'verified_by' => $payment->verifiedBy->name ?? 'System',
            'tenant' => $payment->tenant->name ?? 'Unknown',
            'invoice_id' => $payment->invoice->invoice_id ?? 'N/A',
            'property' => $payment->property->name ?? 'N/A'
        ];
    }
}
