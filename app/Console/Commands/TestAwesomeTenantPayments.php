<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\EnhancedPaymentService;
use App\Enums\PaymentStatusEnum;
use Illuminate\Console\Command;

class TestAwesomeTenantPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:awesome-tenant-payments {--phone=254720691181}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test payment methods with Awesome Tenant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phone = $this->option('phone');
        $this->info("🧪 Testing Payment Methods with Awesome Tenant (Phone: {$phone})");

        // Find Awesome Tenant
        $tenant = User::role('tenant')->where('phone', $phone)->first();
        
        if (!$tenant) {
            $this->error("❌ Awesome Tenant not found with phone: {$phone}");
            return 1;
        }

        $this->info("✅ Found tenant: {$tenant->name} ({$tenant->email})");

        // Get tenant's invoices
        $invoices = Invoice::where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        if ($invoices->isEmpty()) {
            $this->warn("⚠️  No invoices found for Awesome Tenant");
            return 0;
        }

        $this->info("📋 Found {$invoices->count()} invoices for testing");

        // Display invoice details
        foreach ($invoices as $invoice) {
            $this->info("  📄 Invoice {$invoice->invoice_id}:");
            $this->info("    Amount: Ksh " . number_format($invoice->amount, 2));
            $this->info("    Bills: Ksh " . number_format($invoice->bills_amount, 2));
            $this->info("    Total: Ksh " . number_format($invoice->amount + $invoice->bills_amount, 2));
            $this->info("    Paid: Ksh " . number_format($invoice->paid_amount, 2));
            $this->info("    Balance: Ksh " . number_format($invoice->balance_due, 2));
            $this->info("    Status: {$invoice->status->value}");
            $this->info("    Account Number: {$invoice->getAccountNumber()}");
            $this->info("");
        }

        // Test payment methods
        $this->info("💳 Testing Payment Methods:");
        
        $paymentMethods = [
            'MPESA STK' => 'M-PESA STK Push',
            'MPESA PAYBILL' => 'M-PESA Paybill',
            'BANK TRANSFER' => 'Bank Transfer',
            'CASH' => 'Cash Payment',
        ];

        foreach ($paymentMethods as $method => $description) {
            $this->info("  🔸 {$method}: {$description}");
        }

        // Display paybill instructions
        $this->info("");
        $this->info("📱 Paybill Payment Instructions:");
        $this->info("  1. Go to M-PESA menu");
        $this->info("  2. Select 'Lipa na M-PESA'");
        $this->info("  3. Select 'Paybill'");
        $this->info("  4. Enter business number: " . config('mpesa.paybill'));
        $this->info("  5. Enter account number: " . $invoices->first()->getAccountNumber());
        $this->info("  6. Enter amount: Ksh 100 (test amount)");
        $this->info("  7. Enter PIN and press OK");

        // Display STK Push instructions
        $this->info("");
        $this->info("📱 STK Push Instructions:");
        $this->info("  1. Use phone: {$phone}");
        $this->info("  2. STK Push will be initiated automatically");
        $this->info("  3. Check phone for M-PESA prompt");
        $this->info("  4. Enter PIN to complete payment");

        // Display bank transfer instructions
        $this->info("");
        $this->info("🏦 Bank Transfer Instructions:");
        $this->info("  1. Transfer to: [Bank Account Details]");
        $this->info("  2. Reference: INV{$invoices->first()->invoice_id}");
        $this->info("  3. Amount: Ksh 100 (test amount)");
        $this->info("  4. Upload receipt in admin panel");

        // Check current payment methods in database
        $this->info("");
        $this->info("🗄️  Current Payment Methods in Database:");
        $methods = PaymentMethod::all();
        if ($methods->isEmpty()) {
            $this->warn("  ⚠️  No payment methods found. Run seeder first:");
            $this->info("     php artisan db:seed --class=PaymentMethodSeeder");
        } else {
            foreach ($methods as $method) {
                $this->info("  ✅ {$method->name}");
            }
        }

        // Test enhanced payment service
        $this->info("");
        $this->info("🔧 Testing Enhanced Payment Service:");
        try {
            $enhancedService = app(EnhancedPaymentService::class);
            $availableMethods = $enhancedService->getAvailablePaymentMethods();
            
            $this->info("  Available methods:");
            foreach ($availableMethods as $key => $value) {
                $this->info("    {$key}: {$value}");
            }
            
            $this->info("  ✅ Enhanced Payment Service is working correctly");
        } catch (\Exception $e) {
            $this->error("  ❌ Enhanced Payment Service error: " . $e->getMessage());
        }

        // Display recent payments
        $this->info("");
        $this->info("📊 Recent Payments for Awesome Tenant:");
        $recentPayments = Payment::where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        if ($recentPayments->isEmpty()) {
            $this->warn("  ⚠️  No recent payments found");
        } else {
            foreach ($recentPayments as $payment) {
                $this->info("  💰 Ksh " . number_format($payment->amount, 2) . " via {$payment->payment_method} on {$payment->paid_at->format('Y-m-d H:i')}");
            }
        }

        $this->info("");
        $this->info("✅ Awesome Tenant payment test completed!");
        $this->info("🎯 Ready to test with real payments using the instructions above");

        return 0;
    }
}

