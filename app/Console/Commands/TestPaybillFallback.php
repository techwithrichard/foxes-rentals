<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Console\Command;

class TestPaybillFallback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:paybill-fallback {invoice-id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test paybill fallback system for a specific invoice';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $invoiceId = $this->argument('invoice-id');
        
        $this->info("🧪 Testing Paybill Fallback System...");
        $this->newLine();

        // Find the invoice
        $invoice = Invoice::with(['tenant', 'property', 'house'])->find($invoiceId);
        
        if (!$invoice) {
            $this->error("❌ Invoice not found: {$invoiceId}");
            return;
        }

        $this->info("📋 Invoice Details:");
        $this->info("  ID: {$invoice->id}");
        $this->info("  Amount: Ksh " . number_format($invoice->amount, 2));
        $this->info("  Balance Due: Ksh " . number_format($invoice->balance_due, 2));
        $this->info("  Tenant: " . ($invoice->tenant ? $invoice->tenant->name : 'No tenant'));
        $this->info("  Property: " . ($invoice->property ? $invoice->property->name : 'No property'));
        $this->newLine();

        // Get paybill information
        $paybillNumber = config('mpesa.paybill');
        $accountNumber = $invoice->getAccountNumber(); // Use lease reference (e.g., CsBvzmgAmM)
        $amountToPay = ceil($invoice->balance_due);
        $reference = $invoice->lease_reference ?? 'Invoice-' . $invoice->invoice_id;

        $this->info("💳 Paybill Payment Details:");
        $this->info("  Paybill Number: {$paybillNumber}");
        $this->info("  Account Number: {$accountNumber}");
        $this->info("  Amount to Pay: Ksh " . number_format($amountToPay));
        $this->info("  Reference: {$reference}");
        $this->newLine();

        $this->info("📱 M-PESA Instructions:");
        $this->info("  1. Go to M-PESA menu");
        $this->info("  2. Select 'Lipa na M-PESA'");
        $this->info("  3. Select 'Paybill'");
        $this->info("  4. Enter business number: {$paybillNumber}");
        $this->info("  5. Enter account number: {$accountNumber}");
        $this->info("  6. Enter amount: Ksh " . number_format($amountToPay));
        $this->info("  7. Enter PIN and press OK");
        $this->info("  8. You will receive confirmation SMS");
        $this->newLine();

        // Test reconciliation
        $this->info("🔍 Testing Reconciliation:");
        $this->info("  When payment is made with account number '{$accountNumber}',");
        $this->info("  the system should automatically reconcile it with invoice '{$invoice->id}'");
        $this->newLine();

        // Check if tenant exists
        if ($invoice->tenant) {
            $this->info("✅ Tenant found: {$invoice->tenant->name}");
            $this->info("  Phone: " . ($invoice->tenant->phone ?? 'Not set'));
        } else {
            $this->warn("⚠️ No tenant associated with this invoice");
        }

        $this->newLine();
        $this->info("✅ Paybill fallback test completed!");
        $this->info("💡 Use these details to make a test payment and verify reconciliation");
    }
}
