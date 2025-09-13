<?php

namespace App\Console\Commands;

use App\Models\C2bRequest;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Console\Command;

class DiagnosePaybillPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diagnose:paybill-payments {--phone=} {--amount=} {--trans-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnose why paybill payments are not showing up in the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔍 Diagnosing Paybill Payment Issues...");
        $this->newLine();

        // Check recent C2B requests
        $this->checkRecentC2bRequests();
        
        // Check if specific payment exists
        if ($this->option('phone') || $this->option('amount') || $this->option('trans-id')) {
            $this->checkSpecificPayment();
        }
        
        // Check system configuration
        $this->checkSystemConfiguration();
        
        // Check database connectivity
        $this->checkDatabaseConnectivity();
        
        $this->newLine();
        $this->info("✅ Diagnosis complete!");
    }

    private function checkRecentC2bRequests()
    {
        $this->info("📊 Checking Recent C2B Requests...");
        
        $recentRequests = C2bRequest::latest()->take(10)->get();
        
        if ($recentRequests->isEmpty()) {
            $this->warn("❌ No C2B requests found in database!");
            $this->info("This could mean:");
            $this->info("  - M-PESA callbacks are not reaching the system");
            $this->info("  - Database connection issues");
            $this->info("  - C2B requests are being saved to a different table");
            return;
        }
        
        $this->info("✅ Found {$recentRequests->count()} recent C2B requests:");
        
        foreach ($recentRequests as $request) {
            $this->info("  📱 {$request->MSISDN} - Ksh {$request->TransAmount} - {$request->TransID} - {$request->created_at}");
        }
        
        $this->newLine();
    }

    private function checkSpecificPayment()
    {
        $phone = $this->option('phone');
        $amount = $this->option('amount');
        $transId = $this->option('trans-id');
        
        $this->info("🔍 Checking Specific Payment...");
        
        $query = C2bRequest::query();
        
        if ($phone) {
            $query->where('MSISDN', $phone);
            $this->info("  📱 Searching by phone: {$phone}");
        }
        
        if ($amount) {
            $query->where('TransAmount', $amount);
            $this->info("  💰 Searching by amount: {$amount}");
        }
        
        if ($transId) {
            $query->where('TransID', $transId);
            $this->info("  🆔 Searching by transaction ID: {$transId}");
        }
        
        $requests = $query->get();
        
        if ($requests->isEmpty()) {
            $this->warn("❌ No C2B requests found matching criteria!");
            $this->info("This could mean:");
            $this->info("  - Payment was not received by the system");
            $this->info("  - Different phone number format used");
            $this->info("  - Payment was processed differently");
        } else {
            $this->info("✅ Found {$requests->count()} matching C2B requests:");
            
            foreach ($requests as $request) {
                $this->info("  📱 {$request->MSISDN} - Ksh {$request->TransAmount} - {$request->TransID}");
                $this->info("     Status: {$request->reconciliation_status->value}");
                $this->info("     Created: {$request->created_at}");
                
                // Check if payment was created
                $payment = Payment::where('reference_number', $request->TransID)->first();
                if ($payment) {
                    $this->info("     ✅ Payment record exists: {$payment->id}");
                } else {
                    $this->warn("     ❌ No payment record found!");
                }
            }
        }
        
        $this->newLine();
    }

    private function checkSystemConfiguration()
    {
        $this->info("⚙️ Checking System Configuration...");
        
        // Check M-PESA configuration
        $businessShortCode = config('mpesa.business_shortcode');
        $paybill = config('mpesa.paybill');
        $environment = config('mpesa.env');
        
        $this->info("  📋 Business Short Code: {$businessShortCode}");
        $this->info("  📋 Paybill: {$paybill}");
        $this->info("  📋 Environment: {$environment}");
        
        // Check callback URLs
        $confirmationUrl = config('mpesa.confirmation_url');
        $validationUrl = config('mpesa.validation_url');
        
        $this->info("  📋 Confirmation URL: {$confirmationUrl}");
        $this->info("  📋 Validation URL: {$validationUrl}");
        
        $this->newLine();
    }

    private function checkDatabaseConnectivity()
    {
        $this->info("🗄️ Checking Database Connectivity...");
        
        try {
            // Test C2B requests table
            $c2bCount = C2bRequest::count();
            $this->info("  ✅ C2B requests table accessible: {$c2bCount} records");
            
            // Test payments table
            $paymentsCount = Payment::count();
            $this->info("  ✅ Payments table accessible: {$paymentsCount} records");
            
            // Test invoices table
            $invoicesCount = Invoice::count();
            $this->info("  ✅ Invoices table accessible: {$invoicesCount} records");
            
            // Test users table
            $usersCount = User::count();
            $this->info("  ✅ Users table accessible: {$usersCount} records");
            
        } catch (\Exception $e) {
            $this->error("❌ Database connectivity issue: " . $e->getMessage());
        }
        
        $this->newLine();
    }
}

