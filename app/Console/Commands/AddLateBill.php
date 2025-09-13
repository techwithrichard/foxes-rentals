<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class AddLateBill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:late-bill {invoice-uuid} {bill-name} {amount}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a late bill to an existing invoice';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $invoiceUuid = $this->argument('invoice-uuid');
        $billName = $this->argument('bill-name');
        $amount = (float) $this->argument('amount');
        
        $this->info("💰 Adding Late Bill to Invoice...");
        $this->newLine();

        $invoice = Invoice::find($invoiceUuid);
        
        if (!$invoice) {
            $this->error("❌ Invoice not found: {$invoiceUuid}");
            return;
        }

        $this->info("📋 Before Adding Bill:");
        $this->info("  Base Amount: Ksh " . number_format($invoice->amount, 2));
        $this->info("  Bills Amount: Ksh " . number_format($invoice->bills_amount, 2));
        $this->info("  Total Amount: Ksh " . number_format($invoice->amount + $invoice->bills_amount, 2));
        $this->info("  Paid Amount: Ksh " . number_format($invoice->paid_amount, 2));
        $this->info("  Balance Due: Ksh " . number_format($invoice->balance_due, 2));
        $this->info("  Status: {$invoice->status->value}");
        $this->newLine();

        // Add the bill using the new method
        $invoice->addBill($billName, $amount);

        $this->info("✅ Bill Added Successfully!");
        $this->info("  Bill: {$billName}");
        $this->info("  Amount: Ksh " . number_format($amount, 2));
        $this->newLine();

        $this->info("📋 After Adding Bill:");
        $this->info("  Base Amount: Ksh " . number_format($invoice->amount, 2));
        $this->info("  Bills Amount: Ksh " . number_format($invoice->bills_amount, 2));
        $this->info("  Total Amount: Ksh " . number_format($invoice->amount + $invoice->bills_amount, 2));
        $this->info("  Paid Amount: Ksh " . number_format($invoice->paid_amount, 2));
        $this->info("  Balance Due: Ksh " . number_format($invoice->balance_due, 2));
        $this->info("  Status: {$invoice->status->value}");
        $this->newLine();

        // Show bills breakdown
        if ($invoice->bills && is_array($invoice->bills)) {
            $this->info("📄 Bills Breakdown:");
            foreach ($invoice->bills as $bill) {
                $this->info("  - {$bill['name']}: Ksh " . number_format($bill['amount'], 2));
            }
            $this->newLine();
        }

        $this->info("🎯 Paybill Payment Instructions:");
        $this->info("  Paybill Number: " . config('mpesa.paybill'));
        $this->info("  Account Number: {$invoice->getAccountNumber()}");
        $this->info("  Amount to Pay: Ksh " . number_format(ceil($invoice->balance_due)));
        $this->info("  Reference: {$invoice->lease_reference}");
        $this->newLine();

        // Calculate payment scenarios
        $this->info("🧮 Payment Scenarios:");
        $scenarios = [100, 500, 1000, 2000];
        foreach ($scenarios as $scenarioAmount) {
            $newBalance = $invoice->balance_due - $scenarioAmount;
            if ($newBalance <= 0) {
                $overpayment = abs($newBalance);
                $this->info("  Payment Ksh " . number_format($scenarioAmount) . ": " . 
                           ($newBalance == 0 ? "Exact payment" : "Overpayment of Ksh " . number_format($overpayment)));
            } else {
                $this->info("  Payment Ksh " . number_format($scenarioAmount) . ": Remaining balance Ksh " . number_format($newBalance));
            }
        }
    }
}

