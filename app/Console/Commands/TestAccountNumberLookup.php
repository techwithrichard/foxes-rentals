<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class TestAccountNumberLookup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:account-lookup {account-number}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test account number lookup functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $accountNumber = $this->argument('account-number');
        
        $this->info("🔍 Testing Account Number Lookup...");
        $this->newLine();

        $this->info("📋 Looking up account number: {$accountNumber}");
        
        $invoice = Invoice::findByAccountNumber($accountNumber);
        
        if ($invoice) {
            $this->info("✅ Invoice found!");
            $this->info("  Invoice ID: {$invoice->invoice_id}");
            $this->info("  UUID: {$invoice->id}");
            $this->info("  Amount: Ksh " . number_format($invoice->amount, 2));
            $this->info("  Balance Due: Ksh " . number_format($invoice->balance_due, 2));
            $this->info("  Tenant: " . ($invoice->tenant ? $invoice->tenant->name : 'No tenant'));
            $this->info("  Property: " . ($invoice->property ? $invoice->property->name : 'No property'));
            $this->info("  Status: {$invoice->status->value}");
        } else {
            $this->error("❌ No invoice found for account number: {$accountNumber}");
        }

        $this->newLine();
        $this->info("📊 All invoices with their account numbers:");
        
        $invoices = Invoice::all();
        foreach ($invoices as $inv) {
            $accountNumber = $inv->getAccountNumber();
            $this->info("  {$accountNumber} → Invoice ID: {$inv->invoice_id}, Amount: Ksh " . number_format($inv->amount, 2));
        }
    }
}
