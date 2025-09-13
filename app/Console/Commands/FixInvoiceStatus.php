<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class FixInvoiceStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:invoice-status {invoice-uuid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix invoice status based on current balance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $invoiceUuid = $this->argument('invoice-uuid');
        
        $this->info("🔧 Fixing Invoice Status...");
        $this->newLine();

        $invoice = Invoice::find($invoiceUuid);
        
        if (!$invoice) {
            $this->error("❌ Invoice not found: {$invoiceUuid}");
            return;
        }

        $this->info("📋 Before Fix:");
        $this->info("  Base Amount: Ksh " . number_format($invoice->amount, 2));
        $this->info("  Bills Amount: Ksh " . number_format($invoice->bills_amount, 2));
        $this->info("  Total Amount: Ksh " . number_format($invoice->amount + $invoice->bills_amount, 2));
        $this->info("  Paid Amount: Ksh " . number_format($invoice->paid_amount, 2));
        $this->info("  Balance Due: Ksh " . number_format($invoice->balance_due, 2));
        $this->info("  Status: {$invoice->status->value}");
        $this->newLine();

        // Update the status based on current balance
        $invoice->updateStatus();

        $this->info("✅ Status Updated!");
        $this->newLine();

        $this->info("📋 After Fix:");
        $this->info("  Balance Due: Ksh " . number_format($invoice->balance_due, 2));
        $this->info("  Status: {$invoice->status->value}");
        $this->newLine();

        // Show what the status should be
        $totalAmount = $invoice->amount + $invoice->bills_amount;
        $paidAmount = $invoice->paid_amount;
        
        if ($paidAmount == 0) {
            $expectedStatus = 'PENDING';
        } elseif ($paidAmount < $totalAmount) {
            $expectedStatus = 'PARTIALLY_PAID';
        } elseif ($paidAmount == $totalAmount) {
            $expectedStatus = 'PAID';
        } else {
            $expectedStatus = 'OVER_PAID';
        }

        $this->info("🎯 Status Logic:");
        $this->info("  Total Amount: Ksh " . number_format($totalAmount, 2));
        $this->info("  Paid Amount: Ksh " . number_format($paidAmount, 2));
        $this->info("  Expected Status: {$expectedStatus}");
        $this->info("  Actual Status: {$invoice->status->value}");
        
        if ($expectedStatus === $invoice->status->value) {
            $this->info("  ✅ Status is correct!");
        } else {
            $this->warn("  ⚠️ Status mismatch!");
        }
    }
}

