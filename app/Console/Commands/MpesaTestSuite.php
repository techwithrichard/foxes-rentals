<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Invoice;
use App\Services\MPesaHelper;
use Illuminate\Console\Command;

class MpesaTestSuite extends Command
{
    protected $signature = 'mpesa:test-suite {scenario} {--tenant=} {--invoice=}';
    protected $description = 'Comprehensive M-Pesa testing suite';

    public function handle()
    {
        $scenario = $this->argument('scenario');
        
        switch ($scenario) {
            case 'success':
                $this->testSuccessfulPayment();
                break;
            case 'decline':
                $this->testDeclinedPayment();
                break;
            case 'timeout':
                $this->testTimeoutScenario();
                break;
            case 'callback':
                $this->testCallbackHandling();
                break;
            case 'error':
                $this->testErrorHandling();
                break;
            case 'all':
                $this->runAllTests();
                break;
            default:
                $this->showHelp();
        }
        
        return 0;
    }
    
    private function showHelp()
    {
        $this->info('M-Pesa Test Suite - Available Scenarios:');
        $this->line('');
        $this->line('1. success  - Test successful payment flow');
        $this->line('2. decline - Test declined payment flow');
        $this->line('3. timeout - Test timeout scenario');
        $this->line('4. callback - Test callback handling');
        $this->line('5. error   - Test error handling');
        $this->line('6. all     - Run all tests');
        $this->line('');
        $this->line('Usage: php artisan mpesa:test-suite <scenario>');
        $this->line('');
        $this->line('Available Tenants:');
        $this->line('- Jane Tenant (254798765432) - Invoice #1 (KES 25,000)');
        $this->line('- Peter Renter (254734567890) - Invoice #2 (KES 45,000)');
        $this->line('- Dennis Kipk (254725-------+) - Invoice #3 (KES 15,000)');
    }
    
    private function testSuccessfulPayment()
    {
        $this->info('🧪 Testing Successful Payment Flow');
        $this->line('');
        
        // Use Jane Tenant by default or specified tenant
        $tenantName = $this->option('tenant') ?: 'Jane Tenant';
        $tenant = User::where('name', 'LIKE', "%{$tenantName}%")->role('tenant')->first();
        
        if (!$tenant) {
            $this->error("Tenant '{$tenantName}' not found!");
            return;
        }
        
        $this->info("✓ Using tenant: {$tenant->name} ({$tenant->phone})");
        
        // Find unpaid invoice
        $invoice = Invoice::where('tenant_id', $tenant->id)
            ->whereIn('status', ['pending', 'overdue', 'partially_paid'])
            ->first();
            
        if (!$invoice) {
            $this->error("No unpaid invoice found for {$tenant->name}");
            return;
        }
        
        $this->info("✓ Found invoice: #{$invoice->invoice_id} (KES {$invoice->balance_due})");
        $this->line('');
        
        $this->info('📱 Initiating M-Pesa STK Push...');
        $response = MPesaHelper::stkPush(
            $tenant->phone,
            $invoice->balance_due,
            $invoice->lease_reference ?? "Invoice-{$invoice->invoice_id}",
            $tenant->id,
            $invoice->id
        );
        
        if ($response['status'] === 'success') {
            $this->info('✅ STK Push initiated successfully!');
            $this->line("Checkout Request ID: {$response['checkout_request_id']}");
            $this->line("Customer Message: {$response['customer_message']}");
            $this->line('');
            $this->info('📋 Next Steps:');
            $this->line('1. Check your phone for the STK push');
            $this->line('2. Enter your M-Pesa PIN to complete payment');
            $this->line('3. Monitor logs for callback response');
            $this->line('4. Check invoice status after payment');
        } else {
            $this->error('❌ STK Push failed!');
            $this->line("Error: {$response['errorMessage']}");
        }
    }
    
    private function testDeclinedPayment()
    {
        $this->info('🧪 Testing Declined Payment Flow');
        $this->line('');
        $this->warn('This test simulates a declined payment scenario.');
        $this->line('Use Peter Renter for this test.');
        $this->line('');
        
        $tenant = User::where('name', 'Peter Renter')->role('tenant')->first();
        if (!$tenant) {
            $this->error('Peter Renter not found!');
            return;
        }
        
        $this->info("✓ Using tenant: {$tenant->name} ({$tenant->phone})");
        
        $invoice = Invoice::where('tenant_id', $tenant->id)
            ->whereIn('status', ['pending', 'overdue', 'partially_paid'])
            ->first();
            
        if (!$invoice) {
            $this->error("No unpaid invoice found for {$tenant->name}");
            return;
        }
        
        $this->info("✓ Found invoice: #{$invoice->invoice_id} (KES {$invoice->balance_due})");
        $this->line('');
        
        $this->info('📱 Initiating M-Pesa STK Push...');
        $response = MPesaHelper::stkPush(
            $tenant->phone,
            $invoice->balance_due,
            $invoice->lease_reference ?? "Invoice-{$invoice->invoice_id}",
            $tenant->id,
            $invoice->id
        );
        
        if ($response['status'] === 'success') {
            $this->info('✅ STK Push initiated successfully!');
            $this->line("Checkout Request ID: {$response['checkout_request_id']}");
            $this->line('');
            $this->info('📋 Test Instructions:');
            $this->line('1. Check your phone for the STK push');
            $this->line('2. Choose "No" or "Cancel" to decline the payment');
            $this->line('3. Monitor logs for declined callback response');
            $this->line('4. Verify payment marked as failed');
        } else {
            $this->error('❌ STK Push failed!');
            $this->line("Error: {$response['errorMessage']}");
        }
    }
    
    private function testTimeoutScenario()
    {
        $this->info('🧪 Testing Timeout Scenario');
        $this->line('');
        $this->warn('This test simulates a timeout scenario.');
        $this->line('Use Dennis Kipk for this test.');
        $this->line('');
        
        $tenant = User::where('name', 'Dennis Kipk')->role('tenant')->first();
        if (!$tenant) {
            $this->error('Dennis Kipk not found!');
            return;
        }
        
        $this->info("✓ Using tenant: {$tenant->name} ({$tenant->phone})");
        
        $invoice = Invoice::where('tenant_id', $tenant->id)
            ->whereIn('status', ['pending', 'overdue', 'partially_paid'])
            ->first();
            
        if (!$invoice) {
            $this->error("No unpaid invoice found for {$tenant->name}");
            return;
        }
        
        $this->info("✓ Found invoice: #{$invoice->invoice_id} (KES {$invoice->balance_due})");
        $this->line('');
        
        $this->info('📱 Initiating M-Pesa STK Push...');
        $response = MPesaHelper::stkPush(
            $tenant->phone,
            $invoice->balance_due,
            $invoice->lease_reference ?? "Invoice-{$invoice->invoice_id}",
            $tenant->id,
            $invoice->id
        );
        
        if ($response['status'] === 'success') {
            $this->info('✅ STK Push initiated successfully!');
            $this->line("Checkout Request ID: {$response['checkout_request_id']}");
            $this->line('');
            $this->info('📋 Test Instructions:');
            $this->line('1. Check your phone for the STK push');
            $this->line('2. DO NOT respond - let it timeout (usually 2-3 minutes)');
            $this->line('3. Monitor logs for timeout callback response');
            $this->line('4. Verify payment marked as timeout/failed');
        } else {
            $this->error('❌ STK Push failed!');
            $this->line("Error: {$response['errorMessage']}");
        }
    }
    
    private function testCallbackHandling()
    {
        $this->info('🧪 Testing Callback Handling');
        $this->line('');
        $this->info('Available Callback URLs:');
        $this->line('1. STK Callback: ' . config('mpesa.stk_callback_url'));
        $this->line('2. Confirmation URL: ' . config('mpesa.confirmation_url'));
        $this->line('3. Validation URL: ' . config('mpesa.validation_url'));
        $this->line('');
        $this->info('📋 Callback Test Instructions:');
        $this->line('1. Initiate a payment using any tenant');
        $this->line('2. Monitor Laravel logs: storage/logs/laravel.log');
        $this->line('3. Check callback files: storage/app/stk.txt');
        $this->line('4. Verify database updates in stk_requests table');
        $this->line('5. Test callback URL accessibility');
    }
    
    private function testErrorHandling()
    {
        $this->info('🧪 Testing Error Handling');
        $this->line('');
        $this->info('Testing various error scenarios:');
        $this->line('');
        
        // Test with invalid phone number
        $this->line('1. Testing invalid phone number...');
        $response = MPesaHelper::stkPush('123456789', 100, 'TEST-INVALID-PHONE');
        if ($response['status'] === 'failed') {
            $this->info('✅ Invalid phone handled correctly: ' . $response['errorMessage']);
        }
        
        // Test with zero amount
        $this->line('2. Testing zero amount...');
        $response = MPesaHelper::stkPush('254700000000', 0, 'TEST-ZERO-AMOUNT');
        if ($response['status'] === 'failed') {
            $this->info('✅ Zero amount handled correctly: ' . $response['errorMessage']);
        }
        
        $this->line('');
        $this->info('📋 Additional Error Tests:');
        $this->line('- Test with insufficient funds');
        $this->line('- Test with network connectivity issues');
        $this->line('- Test with invalid merchant credentials');
        $this->line('- Test with expired access tokens');
    }
    
    private function runAllTests()
    {
        $this->info('🧪 Running Complete M-Pesa Test Suite');
        $this->line('');
        
        $this->testSuccessfulPayment();
        $this->line('');
        $this->line('---');
        $this->line('');
        
        $this->testDeclinedPayment();
        $this->line('');
        $this->line('---');
        $this->line('');
        
        $this->testTimeoutScenario();
        $this->line('');
        $this->line('---');
        $this->line('');
        
        $this->testCallbackHandling();
        $this->line('');
        $this->line('---');
        $this->line('');
        
        $this->testErrorHandling();
        
        $this->line('');
        $this->info('✅ Complete test suite finished!');
        $this->line('Check logs and database for results.');
    }
}
