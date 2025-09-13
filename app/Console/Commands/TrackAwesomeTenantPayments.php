<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\C2bRequest;
use Illuminate\Console\Command;

class TrackAwesomeTenantPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'track:awesome-tenant-payments {--phone=254720691181} {--watch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Track payments and balance for Awesome Tenant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phone = $this->option('phone');
        $watch = $this->option('watch');
        
        $this->info("📊 Tracking Payments for Awesome Tenant (Phone: {$phone})");
        
        if ($watch) {
            $this->info("👀 Watch mode enabled - monitoring for new payments...");
            $this->watchPayments($phone);
        } else {
            $this->displayCurrentStatus($phone);
        }

        return 0;
    }

    protected function displayCurrentStatus($phone)
    {
        // Find Awesome Tenant
        $tenant = User::role('tenant')->where('phone', $phone)->first();
        
        if (!$tenant) {
            $this->error("❌ Awesome Tenant not found with phone: {$phone}");
            return;
        }

        $this->info("✅ Tenant: {$tenant->name} ({$tenant->email})");

        // Get current invoice
        $invoice = Invoice::where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$invoice) {
            $this->error("❌ No invoice found for Awesome Tenant");
            return;
        }

        // Display invoice details
        $this->info("");
        $this->info("📄 Current Invoice: {$invoice->invoice_id}");
        $this->info("  Base Amount: Ksh " . number_format($invoice->amount, 2));
        $this->info("  Bills Amount: Ksh " . number_format($invoice->bills_amount, 2));
        $this->info("  Total Amount: Ksh " . number_format($invoice->amount + $invoice->bills_amount, 2));
        $this->info("  Paid Amount: Ksh " . number_format($invoice->paid_amount, 2));
        $this->info("  Balance Due: Ksh " . number_format($invoice->balance_due, 2));
        $this->info("  Status: {$invoice->status->value}");
        $this->info("  Account Number: {$invoice->getAccountNumber()}");

        // Display bills breakdown
        if ($invoice->bills && count($invoice->bills) > 0) {
            $this->info("");
            $this->info("🧾 Bills Breakdown:");
            foreach ($invoice->bills as $bill) {
                $this->info("  • {$bill['name']}: Ksh " . number_format($bill['amount'], 2));
            }
        }

        // Display recent payments
        $this->info("");
        $this->info("💰 Recent Payments:");
        $payments = Payment::where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        if ($payments->isEmpty()) {
            $this->warn("  ⚠️  No payments found");
        } else {
            foreach ($payments as $payment) {
                $status = is_object($payment->status) ? $payment->status->value : $payment->status;
                $statusIcon = $status === 'paid' ? '✅' : ($status === 'pending' ? '⏳' : '❌');
                $this->info("  {$statusIcon} Ksh " . number_format($payment->amount, 2) . " via {$payment->payment_method} on {$payment->paid_at->format('Y-m-d H:i')} ({$status})");
            }
        }

        // Display recent C2B requests
        $this->info("");
        $this->info("📱 Recent C2B Requests:");
        $c2bRequests = C2bRequest::where('MSISDN', $phone)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        if ($c2bRequests->isEmpty()) {
            $this->warn("  ⚠️  No C2B requests found");
        } else {
            foreach ($c2bRequests as $request) {
                $this->info("  📞 Ksh " . number_format($request->TransAmount, 2) . " on {$request->created_at->format('Y-m-d H:i')} (ID: {$request->TransID})");
            }
        }

        // Display paybill instructions
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
        $this->info("🔍 Monitoring Commands:");
        $this->info("  php artisan track:awesome-tenant-payments --watch");
        $this->info("  php artisan diagnose:paybill-payments --phone={$phone}");
    }

    protected function watchPayments($phone)
    {
        $this->info("Starting payment monitoring... Press Ctrl+C to stop");
        
        $lastPaymentCount = Payment::whereHas('tenant', function($query) use ($phone) {
            $query->where('phone', $phone);
        })->count();

        $lastC2bCount = C2bRequest::where('MSISDN', $phone)->count();

        while (true) {
            sleep(5); // Check every 5 seconds

            $currentPaymentCount = Payment::whereHas('tenant', function($query) use ($phone) {
                $query->where('phone', $phone);
            })->count();

            $currentC2bCount = C2bRequest::where('MSISDN', $phone)->count();

            if ($currentPaymentCount > $lastPaymentCount) {
                $this->info("🆕 New payment detected! Total payments: {$currentPaymentCount}");
                $this->displayCurrentStatus($phone);
                $lastPaymentCount = $currentPaymentCount;
            }

            if ($currentC2bCount > $lastC2bCount) {
                $this->info("📱 New C2B request detected! Total C2B requests: {$currentC2bCount}");
                $this->displayCurrentStatus($phone);
                $lastC2bCount = $currentC2bCount;
            }

            // Display timestamp every 30 seconds
            if (time() % 30 === 0) {
                $this->info("👀 Monitoring... " . now()->format('H:i:s'));
            }
        }
    }
}
