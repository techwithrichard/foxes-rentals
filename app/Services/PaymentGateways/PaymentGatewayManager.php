<?php

namespace App\Services\PaymentGateways;

use App\Services\PaymentGateways\Contracts\PaymentGatewayInterface;
use App\Services\PaymentGateways\MPesaGateway;
use App\Services\PaymentGateways\BankTransferGateway;
use App\Services\PaymentGateways\CashGateway;
use Illuminate\Support\Facades\Log;

class PaymentGatewayManager
{
    protected $gateways = [];
    protected $defaultGateway = 'mpesa';

    public function __construct()
    {
        $this->registerGateways();
    }

    /**
     * Register available payment gateways
     */
    protected function registerGateways(): void
    {
        $this->gateways = [
            'mpesa' => new MPesaGateway(),
            'bank_transfer' => new BankTransferGateway(),
            'cash' => new CashGateway(),
        ];
    }

    /**
     * Get gateway by name
     */
    public function getGateway(string $gatewayName): ?PaymentGatewayInterface
    {
        return $this->gateways[$gatewayName] ?? null;
    }

    /**
     * Get all available gateways
     */
    public function getAvailableGateways(): array
    {
        $available = [];
        
        foreach ($this->gateways as $name => $gateway) {
            if ($gateway->isAvailable()) {
                $available[$name] = [
                    'name' => $name,
                    'config' => $gateway->getConfig(),
                    'methods' => $gateway->getSupportedMethods()
                ];
            }
        }

        return $available;
    }

    /**
     * Get gateway for payment method
     */
    public function getGatewayForMethod(string $paymentMethod): ?PaymentGatewayInterface
    {
        $methodToGateway = [
            'MPESA_STK' => 'mpesa',
            'MPESA_PAYBILL' => 'mpesa',
            'MPESA_C2B' => 'mpesa',
            'BANK_TRANSFER' => 'bank_transfer',
            'WIRE_TRANSFER' => 'bank_transfer',
            'ACH_TRANSFER' => 'bank_transfer',
            'CASH' => 'cash',
            'CASH_OFFICE' => 'cash',
            'CASH_COLLECTION' => 'cash',
        ];

        $gatewayName = $methodToGateway[$paymentMethod] ?? $this->defaultGateway;
        return $this->getGateway($gatewayName);
    }

    /**
     * Initialize payment with appropriate gateway
     */
    public function initializePayment(array $paymentData): array
    {
        try {
            $paymentMethod = $paymentData['payment_method'] ?? $this->defaultGateway;
            $gateway = $this->getGatewayForMethod($paymentMethod);

            if (!$gateway) {
                return [
                    'success' => false,
                    'error' => "No gateway available for payment method: {$paymentMethod}"
                ];
            }

            // Validate payment data
            $errors = $gateway->validatePaymentData($paymentData);
            if (!empty($errors)) {
                return [
                    'success' => false,
                    'error' => 'Validation failed: ' . implode(', ', $errors)
                ];
            }

            return $gateway->initializePayment($paymentData);

        } catch (\Exception $e) {
            Log::error('Payment initialization failed', [
                'error' => $e->getMessage(),
                'payment_data' => $paymentData
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process payment with appropriate gateway
     */
    public function processPayment($payment): array
    {
        try {
            $gateway = $this->getGatewayForMethod($payment->payment_method);

            if (!$gateway) {
                return [
                    'success' => false,
                    'error' => "No gateway available for payment method: {$payment->payment_method}"
                ];
            }

            return $gateway->processPayment($payment);

        } catch (\Exception $e) {
            Log::error('Payment processing failed', [
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
     * Verify payment with appropriate gateway
     */
    public function verifyPayment(string $reference, string $paymentMethod = null): array
    {
        try {
            $gateway = $paymentMethod ? 
                $this->getGatewayForMethod($paymentMethod) : 
                $this->getGateway($this->defaultGateway);

            if (!$gateway) {
                return [
                    'success' => false,
                    'error' => 'No gateway available for verification'
                ];
            }

            return $gateway->verifyPayment($reference);

        } catch (\Exception $e) {
            Log::error('Payment verification failed', [
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
     * Cancel payment with appropriate gateway
     */
    public function cancelPayment($payment): array
    {
        try {
            $gateway = $this->getGatewayForMethod($payment->payment_method);

            if (!$gateway) {
                return [
                    'success' => false,
                    'error' => "No gateway available for payment method: {$payment->payment_method}"
                ];
            }

            return $gateway->cancelPayment($payment);

        } catch (\Exception $e) {
            Log::error('Payment cancellation failed', [
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
     * Refund payment with appropriate gateway
     */
    public function refundPayment($payment, float $amount = null): array
    {
        try {
            $gateway = $this->getGatewayForMethod($payment->payment_method);

            if (!$gateway) {
                return [
                    'success' => false,
                    'error' => "No gateway available for payment method: {$payment->payment_method}"
                ];
            }

            return $gateway->refundPayment($payment, $amount);

        } catch (\Exception $e) {
            Log::error('Payment refund failed', [
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
     * Get payment status with appropriate gateway
     */
    public function getPaymentStatus(string $reference, string $paymentMethod = null): array
    {
        try {
            $gateway = $paymentMethod ? 
                $this->getGatewayForMethod($paymentMethod) : 
                $this->getGateway($this->defaultGateway);

            if (!$gateway) {
                return [
                    'success' => false,
                    'error' => 'No gateway available for status check'
                ];
            }

            return $gateway->getPaymentStatus($reference);

        } catch (\Exception $e) {
            Log::error('Payment status check failed', [
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
     * Get all supported payment methods
     */
    public function getAllSupportedMethods(): array
    {
        $methods = [];
        
        foreach ($this->gateways as $gateway) {
            if ($gateway->isAvailable()) {
                $methods = array_merge($methods, $gateway->getSupportedMethods());
            }
        }

        return $methods;
    }

    /**
     * Check if payment method is supported
     */
    public function isMethodSupported(string $paymentMethod): bool
    {
        return $this->getGatewayForMethod($paymentMethod) !== null;
    }

    /**
     * Get gateway statistics
     */
    public function getGatewayStatistics(): array
    {
        $stats = [];
        
        foreach ($this->gateways as $name => $gateway) {
            $stats[$name] = [
                'name' => $name,
                'available' => $gateway->isAvailable(),
                'config' => $gateway->getConfig(),
                'methods_count' => count($gateway->getSupportedMethods())
            ];
        }

        return $stats;
    }
}
