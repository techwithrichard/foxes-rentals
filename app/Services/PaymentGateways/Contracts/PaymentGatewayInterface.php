<?php

namespace App\Services\PaymentGateways\Contracts;

use App\Models\Payment;
use App\Models\Invoice;

interface PaymentGatewayInterface
{
    /**
     * Initialize a payment request
     */
    public function initializePayment(array $paymentData): array;

    /**
     * Process a payment
     */
    public function processPayment(Payment $payment): array;

    /**
     * Verify a payment
     */
    public function verifyPayment(string $reference): array;

    /**
     * Cancel a payment
     */
    public function cancelPayment(Payment $payment): array;

    /**
     * Refund a payment
     */
    public function refundPayment(Payment $payment, float $amount = null): array;

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $reference): array;

    /**
     * Get supported payment methods
     */
    public function getSupportedMethods(): array;

    /**
     * Check if gateway is available
     */
    public function isAvailable(): bool;

    /**
     * Get gateway configuration
     */
    public function getConfig(): array;

    /**
     * Validate payment data
     */
    public function validatePaymentData(array $data): array;
}
