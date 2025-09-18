<?php

namespace App\Services\PaymentGateways;

use App\Models\Payment;
use App\Services\PaymentGateways\Contracts\PaymentGatewayInterface;
use App\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\Log;

class BankTransferGateway implements PaymentGatewayInterface
{
    protected $config;

    public function __construct()
    {
        $this->config = config('bank_transfer', []);
    }

    /**
     * Initialize bank transfer payment
     */
    public function initializePayment(array $paymentData): array
    {
        try {
            Log::info('Initializing bank transfer payment', $paymentData);

            // Generate reference number
            $reference = $this->generateReference($paymentData);

            return [
                'success' => true,
                'gateway' => 'bank_transfer',
                'reference' => $reference,
                'message' => 'Bank transfer instructions generated',
                'data' => [
                    'account_number' => $this->config['account_number'] ?? 'N/A',
                    'account_name' => $this->config['account_name'] ?? 'N/A',
                    'bank_name' => $this->config['bank_name'] ?? 'N/A',
                    'branch' => $this->config['branch'] ?? 'N/A',
                    'swift_code' => $this->config['swift_code'] ?? 'N/A',
                    'reference' => $reference,
                    'amount' => $paymentData['amount']
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Bank transfer initialization failed', [
                'error' => $e->getMessage(),
                'payment_data' => $paymentData
            ]);

            return [
                'success' => false,
                'gateway' => 'bank_transfer',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process bank transfer payment
     */
    public function processPayment(Payment $payment): array
    {
        try {
            // Bank transfers require manual verification
            // This method is mainly for status checking
            return [
                'success' => true,
                'status' => 'pending_verification',
                'message' => 'Bank transfer requires manual verification'
            ];

        } catch (\Exception $e) {
            Log::error('Bank transfer processing failed', [
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
     * Verify bank transfer payment
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
                'data' => $payment->toArray()
            ];

        } catch (\Exception $e) {
            Log::error('Bank transfer verification failed', [
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
     * Cancel bank transfer payment
     */
    public function cancelPayment(Payment $payment): array
    {
        try {
            $payment->update(['status' => PaymentStatusEnum::CANCELLED]);

            return [
                'success' => true,
                'message' => 'Bank transfer payment cancelled successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Bank transfer cancellation failed', [
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
     * Refund bank transfer payment
     */
    public function refundPayment(Payment $payment, float $amount = null): array
    {
        try {
            $refundAmount = $amount ?? $payment->amount;
            
            // Create refund record
            $refund = Payment::create([
                'amount' => -$refundAmount, // Negative amount for refund
                'payment_method' => 'BANK_TRANSFER_REFUND',
                'reference_number' => 'REFUND_' . $payment->reference_number,
                'tenant_id' => $payment->tenant_id,
                'invoice_id' => $payment->invoice_id,
                'status' => PaymentStatusEnum::PAID,
                'notes' => "Refund for payment {$payment->reference_number}",
                'parent_payment_id' => $payment->id
            ]);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'amount' => $refundAmount,
                'message' => 'Refund processed successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Bank transfer refund failed', [
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
            'BANK_TRANSFER' => 'Bank Transfer',
            'WIRE_TRANSFER' => 'Wire Transfer',
            'ACH_TRANSFER' => 'ACH Transfer'
        ];
    }

    /**
     * Check if gateway is available
     */
    public function isAvailable(): bool
    {
        return !empty($this->config['account_number']) && 
               !empty($this->config['account_name']) &&
               !empty($this->config['bank_name']);
    }

    /**
     * Get gateway configuration
     */
    public function getConfig(): array
    {
        return [
            'gateway' => 'bank_transfer',
            'account_number' => $this->config['account_number'] ?? null,
            'account_name' => $this->config['account_name'] ?? null,
            'bank_name' => $this->config['bank_name'] ?? null,
            'branch' => $this->config['branch'] ?? null,
            'swift_code' => $this->config['swift_code'] ?? null,
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
     * Generate reference number for bank transfer
     */
    protected function generateReference(array $paymentData): string
    {
        $invoiceId = $paymentData['invoice_id'] ?? 'UNKNOWN';
        $timestamp = now()->format('YmdHis');
        $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        
        return "BT{$invoiceId}{$timestamp}{$random}";
    }

    /**
     * Get bank transfer instructions
     */
    public function getTransferInstructions(array $paymentData): array
    {
        return [
            'account_number' => $this->config['account_number'] ?? 'N/A',
            'account_name' => $this->config['account_name'] ?? 'N/A',
            'bank_name' => $this->config['bank_name'] ?? 'N/A',
            'branch' => $this->config['branch'] ?? 'N/A',
            'swift_code' => $this->config['swift_code'] ?? 'N/A',
            'reference' => $this->generateReference($paymentData),
            'amount' => $paymentData['amount'],
            'instructions' => [
                '1. Log into your bank account',
                '2. Select "Transfer" or "Send Money"',
                '3. Enter the account details above',
                '4. Use the reference number in the description',
                '5. Upload proof of payment'
            ]
        ];
    }
}
