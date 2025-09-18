<?php

namespace App\Services\PaymentGateways;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\StkRequest;
use App\Services\PaymentGateways\Contracts\PaymentGatewayInterface;
use App\Services\MPesaHelper;
use App\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MPesaGateway implements PaymentGatewayInterface
{
    protected $config;

    public function __construct()
    {
        $this->config = config('mpesa');
    }

    /**
     * Initialize STK Push payment
     */
    public function initializePayment(array $paymentData): array
    {
        try {
            $phone = $paymentData['phone'];
            $amount = $paymentData['amount'];
            $reference = $paymentData['reference'] ?? $paymentData['invoice_id'];
            $userId = $paymentData['user_id'] ?? null;
            $invoiceId = $paymentData['invoice_id'] ?? null;

            Log::info('Initializing MPesa STK Push payment', [
                'phone' => $phone,
                'amount' => $amount,
                'reference' => $reference,
                'invoice_id' => $invoiceId
            ]);

            $response = MPesaHelper::stkPush($phone, $amount, $reference, $userId, $invoiceId);

            if ($response['status'] === 'success') {
                return [
                    'success' => true,
                    'gateway' => 'mpesa_stk',
                    'reference' => $response['checkout_request_id'],
                    'message' => $response['customer_message'],
                    'data' => $response
                ];
            }

            return [
                'success' => false,
                'gateway' => 'mpesa_stk',
                'error' => $response['errorMessage'] ?? 'Payment initialization failed',
                'data' => $response
            ];

        } catch (\Exception $e) {
            Log::error('MPesa STK Push initialization failed', [
                'error' => $e->getMessage(),
                'payment_data' => $paymentData
            ]);

            return [
                'success' => false,
                'gateway' => 'mpesa_stk',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process STK Push payment
     */
    public function processPayment(Payment $payment): array
    {
        try {
            // For STK Push, we need to check the status
            $stkRequest = StkRequest::where('CheckoutRequestID', $payment->reference_number)->first();
            
            if (!$stkRequest) {
                return [
                    'success' => false,
                    'error' => 'STK request not found'
                ];
            }

            // Check if payment is already processed
            if ($stkRequest->status === 'Completed') {
                return [
                    'success' => true,
                    'status' => 'completed',
                    'message' => 'Payment already processed'
                ];
            }

            // For STK Push, the actual processing happens via callback
            // This method is mainly for status checking
            return [
                'success' => true,
                'status' => $stkRequest->status,
                'message' => 'STK Push request sent'
            ];

        } catch (\Exception $e) {
            Log::error('MPesa payment processing failed', [
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
     * Verify STK Push payment
     */
    public function verifyPayment(string $reference): array
    {
        try {
            $stkRequest = StkRequest::where('CheckoutRequestID', $reference)->first();
            
            if (!$stkRequest) {
                return [
                    'success' => false,
                    'error' => 'STK request not found'
                ];
            }

            return [
                'success' => true,
                'status' => $stkRequest->status,
                'amount' => $stkRequest->amount,
                'phone' => $stkRequest->phone,
                'reference' => $stkRequest->reference,
                'data' => $stkRequest->toArray()
            ];

        } catch (\Exception $e) {
            Log::error('MPesa payment verification failed', [
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
     * Cancel STK Push payment
     */
    public function cancelPayment(Payment $payment): array
    {
        try {
            $stkRequest = StkRequest::where('CheckoutRequestID', $payment->reference_number)->first();
            
            if ($stkRequest) {
                $stkRequest->update(['status' => 'Cancelled']);
            }

            return [
                'success' => true,
                'message' => 'Payment cancelled successfully'
            ];

        } catch (\Exception $e) {
            Log::error('MPesa payment cancellation failed', [
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
     * Refund payment (not directly supported by STK Push)
     */
    public function refundPayment(Payment $payment, float $amount = null): array
    {
        return [
            'success' => false,
            'error' => 'Refunds not supported for STK Push payments'
        ];
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
            'MPESA_STK' => 'M-PESA STK Push',
            'MPESA_PAYBILL' => 'M-PESA Paybill',
            'MPESA_C2B' => 'M-PESA C2B'
        ];
    }

    /**
     * Check if gateway is available
     */
    public function isAvailable(): bool
    {
        try {
            // Check if MPesa configuration is valid
            return !empty($this->config['consumer_key']) && 
                   !empty($this->config['consumer_secret']) &&
                   !empty($this->config['business_shortcode']) &&
                   !empty($this->config['passkey']);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get gateway configuration
     */
    public function getConfig(): array
    {
        return [
            'gateway' => 'mpesa',
            'environment' => $this->config['env'] ?? 'sandbox',
            'business_shortcode' => $this->config['business_shortcode'] ?? null,
            'paybill' => $this->config['paybill'] ?? null,
            'available' => $this->isAvailable()
        ];
    }

    /**
     * Validate payment data
     */
    public function validatePaymentData(array $data): array
    {
        $errors = [];

        if (!isset($data['phone']) || empty($data['phone'])) {
            $errors[] = 'Phone number is required for MPesa payments';
        }

        if (!isset($data['amount']) || $data['amount'] <= 0) {
            $errors[] = 'Amount must be greater than 0';
        }

        if (isset($data['phone']) && !$this->isValidPhoneNumber($data['phone'])) {
            $errors[] = 'Invalid phone number format';
        }

        return $errors;
    }

    /**
     * Validate phone number format
     */
    protected function isValidPhoneNumber(string $phone): bool
    {
        // Remove any non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Check if it's a valid Kenyan phone number
        return preg_match('/^254[0-9]{9}$/', $phone) || preg_match('/^0[0-9]{9}$/', $phone);
    }

    /**
     * Format phone number to 254 format
     */
    public function formatPhoneNumber(string $phone): string
    {
        // Remove any non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Convert to 254 format if needed
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '254')) {
            $phone = '254' . $phone;
        }
        
        return $phone;
    }

    /**
     * Handle STK Push callback
     */
    public function handleStkCallback(array $callbackData): array
    {
        try {
            $checkoutRequestId = $callbackData['Body']['stkCallback']['CheckoutRequestID'] ?? null;
            $resultCode = $callbackData['Body']['stkCallback']['ResultCode'] ?? null;
            $resultDesc = $callbackData['Body']['stkCallback']['ResultDesc'] ?? null;

            if (!$checkoutRequestId) {
                return [
                    'success' => false,
                    'error' => 'CheckoutRequestID not found in callback'
                ];
            }

            $stkRequest = StkRequest::where('CheckoutRequestID', $checkoutRequestId)->first();
            
            if (!$stkRequest) {
                return [
                    'success' => false,
                    'error' => 'STK request not found'
                ];
            }

            // Update STK request status
            if ($resultCode === 0) {
                $stkRequest->update(['status' => 'Completed']);
                
                // Find and update payment
                $payment = Payment::where('reference_number', $checkoutRequestId)->first();
                if ($payment) {
                    $payment->update(['status' => PaymentStatusEnum::PAID]);
                }
            } else {
                $stkRequest->update(['status' => 'Failed']);
            }

            return [
                'success' => true,
                'result_code' => $resultCode,
                'result_desc' => $resultDesc,
                'status' => $resultCode === 0 ? 'completed' : 'failed'
            ];

        } catch (\Exception $e) {
            Log::error('STK callback handling failed', [
                'callback_data' => $callbackData,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
