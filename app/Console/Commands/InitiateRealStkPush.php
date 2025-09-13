<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Invoice;
use App\Services\MPesaHelper;
use Illuminate\Console\Command;

class InitiateRealStkPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stk:push-real {--phone=254720691181} {--amount=100} {--invoice-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initiate a REAL STK Push to your phone - you will see M-PESA popup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phone = $this->option('phone');
        $amount = $this->option('amount');
        $invoiceId = $this->option('invoice-id');
        
        $this->info("📱 INITIATING REAL STK PUSH TO YOUR PHONE!");
        $this->info("Phone: {$phone}");
        $this->info("Amount: Ksh {$amount}");
        $this->warn("⚠️  CHECK YOUR PHONE NOW - M-PESA POPUP SHOULD APPEAR!");

        // Find tenant
        $tenant = User::role('tenant')->where('phone', $phone)->first();
        
        if (!$tenant) {
            $this->error("❌ Tenant not found with phone: {$phone}");
            return 1;
        }

        $this->info("✅ Found tenant: {$tenant->name}");

        // Get invoice if specified
        $invoice = null;
        if ($invoiceId) {
            $invoice = Invoice::find($invoiceId);
            if ($invoice) {
                $this->info("📄 Using invoice: {$invoice->invoice_id}");
            }
        }

        // Prepare STK Push data
        $reference = $invoice ? "INV{$invoice->invoice_id}" : "TEST" . time();
        
        $this->info("🔧 Preparing STK Push request...");
        $this->info("Reference: {$reference}");
        
        try {
            // Initiate STK Push using MPesaHelper
            $this->info("📡 Sending STK Push request to M-PESA...");
            
            $response = MPesaHelper::stkPush(
                phone: $phone,
                amount: $amount,
                reference: $reference,
                userId: $tenant->id,
                invoiceId: $invoice?->id
            );

            $this->info("📱 STK Push Response:");
            $this->info(json_encode($response, JSON_PRETTY_PRINT));

            if (isset($response['ResponseCode']) && $response['ResponseCode'] == '0') {
                $this->info("✅ STK Push sent successfully!");
                $this->info("📱 CHECK YOUR PHONE NOW!");
                $this->info("   You should see an M-PESA popup asking for PIN");
                $this->info("   Transaction ID: " . ($response['CheckoutRequestID'] ?? 'N/A'));
            } else {
                $this->error("❌ STK Push failed:");
                $this->error("Response Code: " . ($response['ResponseCode'] ?? 'Unknown'));
                $this->error("Response Description: " . ($response['ResponseDescription'] ?? 'Unknown'));
            }

        } catch (\Exception $e) {
            $this->error("❌ Error initiating STK Push: " . $e->getMessage());
            return 1;
        }

        $this->info("");
        $this->info("📱 WHAT TO DO ON YOUR PHONE:");
        $this->info("1. Look for M-PESA popup notification");
        $this->info("2. Tap on the notification");
        $this->info("3. Enter your M-PESA PIN");
        $this->info("4. Confirm the payment");
        $this->info("5. Wait for confirmation SMS");

        $this->info("");
        $this->info("🔍 To monitor the payment:");
        $this->info("php artisan track:awesome-tenant-payments --watch");

        return 0;
    }
}

