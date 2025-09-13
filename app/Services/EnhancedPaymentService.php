<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\User;
use App\Enums\PaymentStatusEnum;
use App\Events\InvoicePaidEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnhancedPaymentService
{
    /**
     * Create a new payment with enhanced invoice synchronization
     * Ensures that any invoice changes (bills, amounts) are reflected in new payments
     */
    public function createPayment(array $paymentData): Payment
    {
        try {
            DB::beginTransaction();

            // Get fresh invoice data to ensure we have the latest information
            $invoice = Invoice::with(['tenant', 'property', 'house'])->findOrFail($paymentData['invoice_id']);
            
            // Refresh invoice to get latest bills and amounts
            $invoice->refresh();
            
            Log::info("Creating payment for invoice {$invoice->invoice_id} with current balance: {$invoice->balance_due}");

            // Calculate current commission and landlord info from fresh invoice data
            $commission = $invoice->commission;
            $landlordId = $invoice->landlord_id;
            $propertyId = $invoice->property_id;
            $houseId = $invoice->house_id;

            // Create payment with fresh invoice data
            $payment = Payment::create([
                'amount' => $paymentData['amount'],
                'paid_at' => $paymentData['paid_at'] ?? now(),
                'payment_method' => $paymentData['payment_method'],
                'reference_number' => $paymentData['reference_number'] ?? null,
                'tenant_id' => $invoice->tenant_id,
                'invoice_id' => $invoice->id,
                'recorded_by' => $paymentData['recorded_by'] ?? null,
                'landlord_id' => $landlordId,
                'commission' => $commission,
                'property_id' => $propertyId,
                'house_id' => $houseId,
                'status' => $paymentData['status'] ?? PaymentStatusEnum::PAID,
                'verified_at' => $paymentData['verified_at'] ?? now(),
                'verified_by' => $paymentData['verified_by'] ?? null,
                'notes' => $paymentData['notes'] ?? null,
                'payment_receipt' => $paymentData['payment_receipt'] ?? null,
            ]);

            // Apply payment to invoice if status is PAID
            if ($payment->status === PaymentStatusEnum::PAID) {
                $this->applyPaymentToInvoice($invoice, $payment);
            }

            DB::commit();

            Log::info("Payment created successfully: {$payment->id} for invoice {$invoice->invoice_id}");

            return $payment;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error creating payment: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Apply payment to invoice and trigger events
     */
    protected function applyPaymentToInvoice(Invoice $invoice, Payment $payment): void
    {
        // Get fresh invoice data before applying payment
        $invoice->refresh();
        
        $currentBalance = $invoice->balance_due;
        $paymentAmount = $payment->amount;
        
        Log::info("Applying payment {$paymentAmount} to invoice {$invoice->invoice_id}. Current balance: {$currentBalance}");

        // Apply payment to invoice
        $invoice->pay($paymentAmount);
        
        // Dispatch event to update invoice status and handle overpayments
        InvoicePaidEvent::dispatch($invoice);
        
        Log::info("Payment applied successfully. New balance: {$invoice->balance_due}");
    }

    /**
     * Create STK Push payment
     */
    public function createStkPayment(array $paymentData): Payment
    {
        $paymentData['payment_method'] = 'MPESA STK';
        $paymentData['status'] = PaymentStatusEnum::PENDING; // STK payments start as pending
        
        return $this->createPayment($paymentData);
    }

    /**
     * Create Paybill payment
     */
    public function createPaybillPayment(array $paymentData): Payment
    {
        $paymentData['payment_method'] = 'MPESA PAYBILL';
        
        return $this->createPayment($paymentData);
    }

    /**
     * Create Bank Transfer payment
     */
    public function createBankPayment(array $paymentData): Payment
    {
        $paymentData['payment_method'] = 'BANK TRANSFER';
        $paymentData['status'] = PaymentStatusEnum::PENDING; // Bank transfers need verification
        
        return $this->createPayment($paymentData);
    }

    /**
     * Update payment status and sync with invoice
     */
    public function updatePaymentStatus(Payment $payment, PaymentStatusEnum $newStatus): Payment
    {
        try {
            DB::beginTransaction();

            $oldStatus = $payment->status;
            $payment->update(['status' => $newStatus]);

            // If payment is being marked as paid, apply it to invoice
            if ($newStatus === PaymentStatusEnum::PAID && $oldStatus !== PaymentStatusEnum::PAID) {
                $invoice = $payment->invoice;
                if ($invoice) {
                    $this->applyPaymentToInvoice($invoice, $payment);
                }
            }

            DB::commit();

            Log::info("Payment status updated: {$payment->id} from {$oldStatus->value} to {$newStatus->value}");

            return $payment->fresh();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating payment status: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get payment methods available for selection
     */
    public function getAvailablePaymentMethods(): array
    {
        return [
            'CASH' => 'Cash Payment',
            'BANK TRANSFER' => 'Bank Transfer',
            'MPESA STK' => 'M-PESA STK Push',
            'MPESA PAYBILL' => 'M-PESA Paybill',
            'MPESA C2B' => 'M-PESA C2B',
            'PAYPAL' => 'PayPal',
            'CHEQUE' => 'Cheque',
            'CARD PAYMENT' => 'Card Payment',
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
        } else {
            $invoice = Invoice::find($data['invoice_id']);
            if (!$invoice) {
                $errors[] = 'Invoice not found';
            }
        }

        if (!isset($data['payment_method']) || empty($data['payment_method'])) {
            $errors[] = 'Payment method is required';
        }

        return $errors;
    }
}

