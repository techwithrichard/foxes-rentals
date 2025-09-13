<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Invoice;
use Illuminate\Console\Command;

class AddBillsToAwesomeTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:bills-awesome-tenant {--garbage=5} {--water=3}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add garbage and water bills to Awesome Tenant invoice';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $garbageAmount = $this->option('garbage');
        $waterAmount = $this->option('water');
        
        $this->info("🧾 Adding bills to Awesome Tenant invoice...");
        $this->info("  Garbage Bill: Ksh {$garbageAmount}");
        $this->info("  Water Bill: Ksh {$waterAmount}");

        // Find Awesome Tenant
        $tenant = User::role('tenant')->where('phone', '254720691181')->first();
        
        if (!$tenant) {
            $this->error("❌ Awesome Tenant not found with phone: 254720691181");
            return 1;
        }

        $this->info("✅ Found tenant: {$tenant->name}");

        // Get the latest invoice
        $invoice = Invoice::where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$invoice) {
            $this->error("❌ No invoice found for Awesome Tenant");
            return 1;
        }

        $this->info("📄 Current Invoice: {$invoice->invoice_id}");
        $this->info("  Base Amount: Ksh " . number_format($invoice->amount, 2));
        $this->info("  Current Bills: Ksh " . number_format($invoice->bills_amount, 2));
        $this->info("  Paid Amount: Ksh " . number_format($invoice->paid_amount, 2));
        $this->info("  Current Balance: Ksh " . number_format($invoice->balance_due, 2));

        // Add bills to invoice
        $bills = [
            ['name' => 'Garbage Collection', 'amount' => $garbageAmount],
            ['name' => 'Water Bill', 'amount' => $waterAmount],
        ];

        $this->info("➕ Adding bills to invoice...");
        
        try {
            $invoice->addBills($bills);
            $invoice->refresh(); // Refresh to get updated data
            
            $this->info("✅ Bills added successfully!");
            $this->info("📊 Updated Invoice Details:");
            $this->info("  Base Amount: Ksh " . number_format($invoice->amount, 2));
            $this->info("  Bills Amount: Ksh " . number_format($invoice->bills_amount, 2));
            $this->info("  Total Amount: Ksh " . number_format($invoice->amount + $invoice->bills_amount, 2));
            $this->info("  Paid Amount: Ksh " . number_format($invoice->paid_amount, 2));
            $this->info("  New Balance: Ksh " . number_format($invoice->balance_due, 2));
            $this->info("  Status: {$invoice->status->value}");
            $this->info("  Account Number: {$invoice->getAccountNumber()}");

            // Display paybill payment instructions
            $this->info("");
            $this->info("📱 Paybill Payment Instructions:");
            $this->info("  1. Go to M-PESA menu");
            $this->info("  2. Select 'Lipa na M-PESA'");
            $this->info("  3. Select 'Paybill'");
            $this->info("  4. Enter business number: " . config('mpesa.paybill'));
            $this->info("  5. Enter account number: {$invoice->getAccountNumber()}");
            $this->info("  6. Enter amount: Ksh " . number_format($invoice->balance_due, 2));
            $this->info("  7. Enter PIN and press OK");

            $this->info("");
            $this->info("🎯 Ready for paybill payment! Use the instructions above to make payment.");

        } catch (\Exception $e) {
            $this->error("❌ Error adding bills: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}

