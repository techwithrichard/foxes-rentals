<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\StkRequest;
use App\Models\C2bRequest;
use App\Models\Overpayment;
use App\Models\User;
use App\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentReconciliationService
{
    /**
     * Automatically reconcile payments based on phone number and amount
     * Always prioritizes balance accuracy - whether single or multiple payments
     */
    public function autoReconcileByPhoneAndAmount($phone, $amount, $referenceNumber = null)
    {
        try {
            // Find tenant by phone number
            $tenant = User::role('tenant')->where('phone', $phone)->first();
            
            if (!$tenant) {
                Log::warning("Auto-reconciliation failed: Tenant not found for phone {$phone}");
                return [
                    'success' => false,
                    'message' => 'Tenant not found for the provided phone number'
                ];
            }

            // Find unpaid invoices for this tenant (oldest first)
            $unpaidInvoices = Invoice::where('tenant_id', $tenant->id)
                ->whereIn('status', [
                    PaymentStatusEnum::PENDING, 
                    PaymentStatusEnum::PARTIALLY_PAID, 
                    PaymentStatusEnum::OVERDUE
                ])
                ->orderBy('created_at', 'asc')
                ->get();

            if ($unpaidInvoices->isEmpty()) {
                Log::warning("Auto-reconciliation failed: No unpaid invoices found for tenant {$tenant->id}");
                return [
                    'success' => false,
                    'message' => 'No unpaid invoices found for this tenant'
                ];
            }

            // Process each invoice to find the best match based on balance
            foreach ($unpaidInvoices as $invoice) {
                $totalAmount = $invoice->amount + $invoice->bills_amount;
                $paidAmount = $invoice->paid_amount;
                $balance = $totalAmount - $paidAmount;
                
                Log::info("Checking invoice {$invoice->invoice_id}: Total {$totalAmount}, Paid {$paidAmount}, Balance {$balance}, Payment {$amount}");
                
                // If payment exactly matches balance, complete the invoice
                if ($amount == $balance && $balance > 0) {
                    Log::info("Exact match found for invoice {$invoice->invoice_id}");
                    return $this->reconcilePayment($invoice, $amount, $referenceNumber, $tenant);
                }
                
                // If payment is less than balance, it's a partial payment
                if ($amount < $balance && $balance > 0) {
                    Log::info("Partial payment match found for invoice {$invoice->invoice_id}");
                    return $this->reconcilePayment($invoice, $amount, $referenceNumber, $tenant);
                }
                
                // If payment exceeds balance, it's an overpayment
                if ($amount > $balance && $balance > 0) {
                    Log::info("Overpayment detected for invoice {$invoice->invoice_id}");
                    return $this->handleDuplicatePayment($phone, $amount, $referenceNumber, [
                        'invoice' => $invoice,
                        'overpayment_amount' => $amount - $balance,
                        'remaining_balance' => $balance,
                        'total_invoice_amount' => $totalAmount,
                        'paid_amount' => $paidAmount
                    ]);
                }
            }

            // If no invoice can absorb this payment, create overpayment
            Log::info("No invoice can absorb payment {$amount}, creating overpayment");
            return $this->createOverpayment($tenant, $amount, $referenceNumber);

        } catch (\Exception $e) {
            Log::error("Auto-reconciliation error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error during auto-reconciliation: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Reconcile payment with specific invoice
     * Always ensures balance accuracy regardless of payment frequency
     */
    private function reconcilePayment($invoice, $amount, $referenceNumber, $tenant)
    {
        try {
            DB::beginTransaction();

            // Calculate current balance before payment
            $totalAmount = $invoice->amount + $invoice->bills_amount;
            $currentPaidAmount = $invoice->paid_amount;
            $currentBalance = $totalAmount - $currentPaidAmount;
            
            Log::info("Reconciling payment: Invoice {$invoice->invoice_id}, Total: {$totalAmount}, Current Paid: {$currentPaidAmount}, Current Balance: {$currentBalance}, Payment: {$amount}");

            // Create payment record
            $payment = Payment::create([
                'amount' => $amount,
                'paid_at' => now(),
                'payment_method' => 'MPESA C2B',
                'reference_number' => $referenceNumber,
                'tenant_id' => $tenant->id,
                'invoice_id' => $invoice->id,
                'recorded_by' => null, // System recorded
                'landlord_id' => $invoice->landlord_id,
                'commission' => $invoice->commission,
                'property_id' => $invoice->property_id,
                'house_id' => $invoice->house_id,
                'status' => PaymentStatusEnum::PAID,
                'verified_at' => now(),
                'verified_by' => null, // Auto-verified
                'notes' => "Auto-reconciled payment. Balance before: {$currentBalance}, Payment: {$amount}",
            ]);

            // Update invoice with payment
            $invoice->pay($amount);
            
            // Refresh invoice to get updated paid amount
            $invoice->refresh();
            $newPaidAmount = $invoice->paid_amount;
            $newBalance = $totalAmount - $newPaidAmount;
            
            Log::info("Payment applied: Invoice {$invoice->invoice_id}, New Paid: {$newPaidAmount}, New Balance: {$newBalance}");

            // Check if this payment caused an overpayment
            if ($newPaidAmount > $totalAmount) {
                $overpaymentAmount = $newPaidAmount - $totalAmount;
                Log::info("Overpayment detected after payment: {$overpaymentAmount}");
                
                // Create overpayment record
                $existingOverpayment = Overpayment::where('tenant_id', $tenant->id)->first();
                if ($existingOverpayment) {
                    $existingOverpayment->update(['amount' => $existingOverpayment->amount + $overpaymentAmount]);
                } else {
                    Overpayment::create([
                        'tenant_id' => $tenant->id,
                        'amount' => $overpaymentAmount,
                    ]);
                }
            }

            DB::commit();

            Log::info("Payment auto-reconciled successfully: {$referenceNumber} for invoice {$invoice->invoice_id}, Final balance: {$newBalance}");

            return [
                'success' => true,
                'message' => 'Payment automatically reconciled with invoice',
                'payment_id' => $payment->id,
                'invoice_id' => $invoice->id,
                'final_balance' => $newBalance,
                'overpayment' => $newPaidAmount > $totalAmount,
                'overpayment_amount' => $newPaidAmount > $totalAmount ? $newPaidAmount - $totalAmount : 0
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Payment reconciliation error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error reconciling payment: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create overpayment record
     */
    private function createOverpayment($tenant, $amount, $referenceNumber)
    {
        try {
            DB::beginTransaction();

            // Create payment record without invoice
            $payment = Payment::create([
                'amount' => $amount,
                'paid_at' => now(),
                'payment_method' => 'MPESA C2B',
                'reference_number' => $referenceNumber,
                'tenant_id' => $tenant->id,
                'invoice_id' => null,
                'recorded_by' => null,
                'landlord_id' => null,
                'commission' => 0,
                'property_id' => null,
                'house_id' => null,
                'status' => PaymentStatusEnum::PAID,
                'verified_at' => null, // Needs manual verification
                'verified_by' => null,
                'notes' => 'Auto-recorded as overpayment - needs manual verification',
            ]);

            // Create overpayment record
            Overpayment::create([
                'tenant_id' => $tenant->id,
                'amount' => $amount,
            ]);

            DB::commit();

            Log::info("Overpayment created: {$referenceNumber} for tenant {$tenant->id}");

            return [
                'success' => true,
                'message' => 'Payment recorded as overpayment - requires manual verification',
                'payment_id' => $payment->id,
                'overpayment' => true
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Overpayment creation error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error creating overpayment: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process C2B callback and attempt auto-reconciliation
     */
    public function processC2bCallback($c2bData)
    {
        $phone = $c2bData['MSISDN'] ?? null;
        $amount = $c2bData['TransAmount'] ?? null;
        $referenceNumber = $c2bData['TransID'] ?? null;
        $accountNumber = $c2bData['BillRefNumber'] ?? null;

        if (!$phone || !$amount || !$referenceNumber) {
            return [
                'success' => false,
                'message' => 'Missing required C2B data'
            ];
        }

        // Check if payment already exists by reference number
        $existingPayment = Payment::where('reference_number', $referenceNumber)->first();
        if ($existingPayment) {
            Log::info("Duplicate payment detected by reference number: {$referenceNumber}");
            return [
                'success' => false,
                'message' => 'Payment already exists with this reference number'
            ];
        }

        // If account number is provided, try to reconcile by account number first
        if ($accountNumber) {
            $accountReconciliation = $this->reconcileByAccountNumber($accountNumber, $amount, $referenceNumber, $phone);
            if ($accountReconciliation['success']) {
                return $accountReconciliation;
            }
        }

        // Check if this payment would cause an overpayment
        $overpaymentCheck = $this->checkForDuplicatePayment($phone, $amount);
        if ($overpaymentCheck['is_duplicate']) {
            Log::info("Overpayment detected: Phone {$phone}, Amount {$amount}, Invoice: {$overpaymentCheck['invoice']->invoice_id}, Overpayment: {$overpaymentCheck['overpayment_amount']}");
            
            // Handle as overpayment by splitting the payment
            return $this->handleDuplicatePayment($phone, $amount, $referenceNumber, $overpaymentCheck);
        }

        // Attempt auto-reconciliation
        return $this->autoReconcileByPhoneAndAmount($phone, $amount, $referenceNumber);
    }

    /**
     * Reconcile payment by account number (short format like INV000001)
     */
    public function reconcileByAccountNumber($accountNumber, $amount, $referenceNumber, $phone = null)
    {
        try {
            // Find invoice by account number (lease reference preferred)
            $invoice = Invoice::findByAccountNumber($accountNumber);
            
            if (!$invoice) {
                Log::warning("Account number reconciliation failed: Invoice not found for account {$accountNumber}");
                return [
                    'success' => false,
                    'message' => 'Invoice not found for the provided account number'
                ];
            }

            // Verify phone number matches if provided
            if ($phone && $invoice->tenant && $invoice->tenant->phone !== $phone) {
                Log::warning("Account number reconciliation failed: Phone mismatch for account {$accountNumber}. Expected: {$invoice->tenant->phone}, Got: {$phone}");
                return [
                    'success' => false,
                    'message' => 'Phone number does not match the invoice tenant'
                ];
            }

            // Check if invoice is already paid
            if ($invoice->status === PaymentStatusEnum::PAID || $invoice->status === PaymentStatusEnum::OVER_PAID) {
                Log::info("Account number reconciliation: Invoice {$invoice->invoice_id} is already fully paid");
                return [
                    'success' => false,
                    'message' => 'Invoice is already fully paid'
                ];
            }

            // Calculate current balance
            $currentBalance = $invoice->balance_due;
            
            if ($amount > $currentBalance) {
                // This is an overpayment - split the payment
                Log::info("Account number reconciliation: Overpayment detected for invoice {$invoice->invoice_id}. Amount: {$amount}, Balance: {$currentBalance}");
                
                return $this->handleOverpayment($invoice, $amount, $referenceNumber, $currentBalance);
            } else {
                // Regular payment
                Log::info("Account number reconciliation: Regular payment for invoice {$invoice->invoice_id}. Amount: {$amount}, Balance: {$currentBalance}");
                
                return $this->reconcilePayment($invoice, $amount, $referenceNumber);
            }

        } catch (\Exception $e) {
            Log::error("Account number reconciliation error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error processing account number reconciliation: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get reconciliation suggestions for a payment
     */
    public function getReconciliationSuggestions($payment)
    {
        $suggestions = [];

        if (!$payment->tenant) {
            return $suggestions;
        }

        // Get unpaid invoices for the tenant
        $unpaidInvoices = Invoice::where('tenant_id', $payment->tenant_id)
            ->where('status', '!=', PaymentStatusEnum::PAID)
            ->get();

        foreach ($unpaidInvoices as $invoice) {
            $balance = ($invoice->amount + $invoice->bills_amount) - $invoice->paid_amount;
            
            if ($balance > 0) {
                $suggestions[] = [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_id,
                    'property_name' => $invoice->property?->name,
                    'house_name' => $invoice->house?->name,
                    'total_amount' => $invoice->amount + $invoice->bills_amount,
                    'balance' => $balance,
                    'match_score' => $this->calculateMatchScore($payment->amount, $balance),
                ];
            }
        }

        // Sort by match score (highest first)
        usort($suggestions, function ($a, $b) {
            return $b['match_score'] <=> $a['match_score'];
        });

        return $suggestions;
    }

    /**
     * Calculate match score between payment amount and invoice balance
     */
    private function calculateMatchScore($paymentAmount, $invoiceBalance)
    {
        if ($paymentAmount == $invoiceBalance) {
            return 100; // Perfect match
        }

        if ($paymentAmount > $invoiceBalance) {
            return 80; // Overpayment
        }

        // Partial payment - calculate percentage
        $percentage = ($paymentAmount / $invoiceBalance) * 100;
        return min($percentage, 70); // Cap at 70% for partial payments
    }

    /**
     * Check if payment would cause overpayment for any invoice
     * Always prioritizes balance accuracy over payment frequency
     */
    public function checkForDuplicatePayment($phone, $amount)
    {
        // Find tenant by phone number
        $tenant = User::role('tenant')->where('phone', $phone)->first();
        
        if (!$tenant) {
            return ['is_duplicate' => false];
        }

        // Get all unpaid or partially paid invoices for this tenant
        $invoices = Invoice::where('tenant_id', $tenant->id)
            ->whereIn('status', [
                PaymentStatusEnum::PENDING, 
                PaymentStatusEnum::PARTIALLY_PAID, 
                PaymentStatusEnum::OVERDUE
            ])
            ->orderBy('created_at', 'asc') // Process oldest invoices first
            ->get();

        foreach ($invoices as $invoice) {
            $totalAmount = $invoice->amount + $invoice->bills_amount;
            $paidAmount = $invoice->paid_amount;
            $balance = $totalAmount - $paidAmount;
            
            Log::info("Invoice {$invoice->invoice_id} - Total: {$totalAmount}, Paid: {$paidAmount}, Balance: {$balance}, Payment: {$amount}");
            
            // If this payment would exceed the balance, it's an overpayment
            if ($amount > $balance && $balance > 0) {
                Log::info("Overpayment detected for invoice {$invoice->invoice_id}: Payment {$amount} exceeds balance {$balance}");
                return [
                    'is_duplicate' => true,
                    'invoice' => $invoice,
                    'overpayment_amount' => $amount - $balance,
                    'remaining_balance' => $balance,
                    'total_invoice_amount' => $totalAmount,
                    'paid_amount' => $paidAmount
                ];
            }
            
            // If payment exactly matches balance, it will complete the invoice
            if ($amount == $balance && $balance > 0) {
                Log::info("Exact payment for invoice {$invoice->invoice_id}: Payment {$amount} matches balance {$balance}");
                return [
                    'is_duplicate' => false,
                    'invoice' => $invoice,
                    'exact_match' => true,
                    'remaining_balance' => $balance,
                    'total_invoice_amount' => $totalAmount
                ];
            }
            
            // If payment is less than balance, it's a partial payment
            if ($amount < $balance && $balance > 0) {
                Log::info("Partial payment for invoice {$invoice->invoice_id}: Payment {$amount} less than balance {$balance}");
                return [
                    'is_duplicate' => false,
                    'invoice' => $invoice,
                    'partial_payment' => true,
                    'remaining_balance' => $balance - $amount,
                    'total_invoice_amount' => $totalAmount
                ];
            }
        }

        Log::info("No matching invoice found for payment {$amount} from phone {$phone}");
        return ['is_duplicate' => false];
    }

    /**
     * Handle overpayment by splitting payment between invoice and overpayment
     */
    public function handleDuplicatePayment($phone, $amount, $referenceNumber, $overpaymentData)
    {
        try {
            // Find tenant by phone number
            $tenant = User::role('tenant')->where('phone', $phone)->first();
            
            if (!$tenant) {
                return [
                    'success' => false,
                    'message' => 'Tenant not found for overpayment handling'
                ];
            }

            $invoice = $overpaymentData['invoice'];
            $overpaymentAmount = $overpaymentData['overpayment_amount'];
            $remainingBalance = $overpaymentData['remaining_balance'];

            DB::beginTransaction();

            // First, create payment for the remaining balance of the invoice
            $invoicePayment = Payment::create([
                'amount' => $remainingBalance,
                'paid_at' => now(),
                'payment_method' => 'MPESA C2B',
                'reference_number' => $referenceNumber . '-INVOICE',
                'tenant_id' => $tenant->id,
                'invoice_id' => $invoice->id,
                'recorded_by' => null,
                'landlord_id' => $invoice->landlord_id,
                'commission' => $invoice->commission,
                'property_id' => $invoice->property_id,
                'house_id' => $invoice->house_id,
                'status' => PaymentStatusEnum::PAID,
                'verified_at' => now(),
                'verified_by' => null,
                'notes' => "Payment for invoice balance - remaining amount after overpayment",
            ]);

            // Pay the remaining balance to complete the invoice
            $invoice->pay($remainingBalance);
            \App\Events\InvoicePaidEvent::dispatch($invoice);

            // Create overpayment record for the excess amount
            $overpaymentPayment = Payment::create([
                'amount' => $overpaymentAmount,
                'paid_at' => now(),
                'payment_method' => 'MPESA C2B',
                'reference_number' => $referenceNumber . '-OVERPAYMENT',
                'tenant_id' => $tenant->id,
                'invoice_id' => null, // No invoice - this is an overpayment
                'recorded_by' => null,
                'landlord_id' => null,
                'commission' => 0,
                'property_id' => null,
                'house_id' => null,
                'status' => PaymentStatusEnum::PAID,
                'verified_at' => now(),
                'verified_by' => null,
                'notes' => "Overpayment amount - excess payment above invoice balance. Invoice: {$invoice->invoice_id}, Overpaid by: {$overpaymentAmount}",
            ]);

            // Create or update overpayment record
            $existingOverpayment = Overpayment::where('tenant_id', $tenant->id)->first();
            
            if ($existingOverpayment) {
                $existingOverpayment->update(['amount' => $existingOverpayment->amount + $overpaymentAmount]);
            } else {
                Overpayment::create([
                    'tenant_id' => $tenant->id,
                    'amount' => $overpaymentAmount,
                ]);
            }

            DB::commit();

            Log::info("Overpayment handled: {$referenceNumber} for tenant {$tenant->id}, invoice: {$invoice->invoice_id}, overpaid by: {$overpaymentAmount}");

            return [
                'success' => true,
                'message' => "Payment split: {$remainingBalance} for invoice, {$overpaymentAmount} as overpayment",
                'invoice_payment_id' => $invoicePayment->id,
                'overpayment_payment_id' => $overpaymentPayment->id,
                'overpayment_amount' => $overpaymentAmount,
                'invoice_completed' => true
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error handling overpayment: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error handling overpayment: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Test method to simulate duplicate payment detection
     * This can be used for testing or manual verification
     */
    public function testDuplicatePaymentDetection($phone, $amount)
    {
        $duplicateCheck = $this->checkForDuplicatePayment($phone, $amount);
        
        Log::info("Duplicate payment test for phone {$phone}, amount {$amount}: " . json_encode($duplicateCheck));
        
        return $duplicateCheck;
    }
}
