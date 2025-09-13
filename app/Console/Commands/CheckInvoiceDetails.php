<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class CheckInvoiceDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:invoice-details {invoice-uuid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check detailed invoice information including bills and payments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $invoiceUuid = $this->argument('invoice-uuid');
        
        $this->info("🔍 Checking Invoice Details...");
        $this->newLine();

        $invoice = Invoice::with(['tenant', 'property', 'house', 'payments'])->find($invoiceUuid);
        
        if (!$invoice) {
            $this->error("❌ Invoice not found: {$invoiceUuid}");
            return;
        }

        $this->info("📋 Invoice Information:");
        $this->info("  UUID: {$invoice->id}");
        $this->info("  Invoice ID: {$invoice->invoice_id}");
        $this->info("  Lease Reference: {$invoice->lease_reference}");
        $this->info("  Account Number: {$invoice->getAccountNumber()}");
        $this->newLine();

        $this->info("💰 Financial Details:");
        $this->info("  Base Amount: Ksh " . number_format($invoice->amount, 2));
        $this->info("  Bills Amount: Ksh " . number_format($invoice->bills_amount, 2));
        $this->info("  Total Amount: Ksh " . number_format($invoice->amount + $invoice->bills_amount, 2));
        $this->info("  Paid Amount: Ksh " . number_format($invoice->paid_amount, 2));
        $this->info("  Balance Due: Ksh " . number_format($invoice->balance_due, 2));
        $this->info("  Status: {$invoice->status->value}");
        $this->newLine();

        if ($invoice->bills && is_array($invoice->bills)) {
            $this->info("📄 Bills Breakdown:");
            foreach ($invoice->bills as $bill) {
                $this->info("  - {$bill['name']}: Ksh " . number_format($bill['amount'], 2));
            }
            $this->newLine();
        }

        $this->info("👤 Tenant Information:");
        $this->info("  Name: " . ($invoice->tenant ? $invoice->tenant->name : 'No tenant'));
        $this->info("  Phone: " . ($invoice->tenant ? $invoice->tenant->phone : 'No phone'));
        $this->info("  Property: " . ($invoice->property ? $invoice->property->name : 'No property'));
        $this->newLine();

        $this->info("💳 Payment History:");
        if ($invoice->payments->count() > 0) {
            foreach ($invoice->payments as $payment) {
                $status = is_object($payment->status) ? $payment->status->value : $payment->status;
                $this->info("  - Amount: Ksh " . number_format($payment->amount, 2) . 
                           " | Status: {$status}" . 
                           " | Date: {$payment->created_at->format('Y-m-d H:i:s')}" .
                           " | Reference: {$payment->reference_number}");
            }
        } else {
            $this->info("  No payments recorded yet");
        }
        $this->newLine();

        $this->info("🎯 Paybill Payment Instructions:");
        $this->info("  Paybill Number: " . config('mpesa.paybill'));
        $this->info("  Account Number: {$invoice->getAccountNumber()}");
        $this->info("  Amount to Pay: Ksh " . number_format(ceil($invoice->balance_due)));
        $this->info("  Reference: {$invoice->lease_reference}");
        $this->newLine();

        // Calculate what happens with different payment amounts
        $this->info("🧮 Payment Scenarios:");
        $scenarios = [100, 500, 1000, 2000];
        foreach ($scenarios as $amount) {
            $newBalance = $invoice->balance_due - $amount;
            if ($newBalance <= 0) {
                $overpayment = abs($newBalance);
                $this->info("  Payment Ksh " . number_format($amount) . ": " . 
                           ($newBalance == 0 ? "Exact payment" : "Overpayment of Ksh " . number_format($overpayment)));
            } else {
                $this->info("  Payment Ksh " . number_format($amount) . ": Remaining balance Ksh " . number_format($newBalance));
            }
        }
    }
}
