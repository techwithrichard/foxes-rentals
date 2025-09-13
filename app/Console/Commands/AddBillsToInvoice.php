<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class AddBillsToInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:bills-to-invoice {invoice-uuid} {--water=500} {--garbage=300}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add water and garbage bills to an invoice';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $invoiceUuid = $this->argument('invoice-uuid');
        $waterAmount = $this->option('water');
        $garbageAmount = $this->option('garbage');
        
        $this->info("💰 Adding Bills to Invoice...");
        $this->newLine();

        $invoice = Invoice::find($invoiceUuid);
        
        if (!$invoice) {
            $this->error("❌ Invoice not found: {$invoiceUuid}");
            return;
        }

        $this->info("📋 Current Invoice Status:");
        $this->info("  Base Amount: Ksh " . number_format($invoice->amount, 2));
        $this->info("  Bills Amount: Ksh " . number_format($invoice->bills_amount, 2));
        $this->info("  Paid Amount: Ksh " . number_format($invoice->paid_amount, 2));
        $this->info("  Balance Due: Ksh " . number_format($invoice->balance_due, 2));
        $this->newLine();

        // Create bills array
        $bills = [
            ['name' => 'Water Bill', 'amount' => $waterAmount],
            ['name' => 'Garbage Collection', 'amount' => $garbageAmount]
        ];

        $totalBillsAmount = $waterAmount + $garbageAmount;

        $this->info("📄 Adding Bills:");
        foreach ($bills as $bill) {
            $this->info("  - {$bill['name']}: Ksh " . number_format($bill['amount'], 2));
        }
        $this->info("  Total Bills: Ksh " . number_format($totalBillsAmount, 2));
        $this->newLine();

        // Update the invoice
        $invoice->bills = $bills;
        $invoice->bills_amount = $totalBillsAmount;
        $invoice->save();

        // Refresh to get updated balance
        $invoice->refresh();

        $this->info("✅ Bills Added Successfully!");
        $this->newLine();

        $this->info("📋 Updated Invoice Status:");
        $this->info("  Base Amount: Ksh " . number_format($invoice->amount, 2));
        $this->info("  Bills Amount: Ksh " . number_format($invoice->bills_amount, 2));
        $this->info("  Total Amount: Ksh " . number_format($invoice->amount + $invoice->bills_amount, 2));
        $this->info("  Paid Amount: Ksh " . number_format($invoice->paid_amount, 2));
        $this->info("  Balance Due: Ksh " . number_format($invoice->balance_due, 2));
        $this->info("  Status: {$invoice->status->value}");
        $this->newLine();

        $this->info("🎯 Paybill Payment Instructions:");
        $this->info("  Paybill Number: " . config('mpesa.paybill'));
        $this->info("  Account Number: {$invoice->getAccountNumber()}");
        $this->info("  Amount to Pay: Ksh " . number_format(ceil($invoice->balance_due)));
        $this->info("  Reference: {$invoice->lease_reference}");
        $this->newLine();

        // Calculate payment scenarios
        $this->info("🧮 Payment Scenarios:");
        $scenarios = [500, 1000, 1500, 2000];
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

