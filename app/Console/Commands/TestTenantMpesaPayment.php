<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Invoice;
use App\Services\MPesaHelper;
use Illuminate\Console\Command;

class TestTenantMpesaPayment extends Command
{
    protected $signature = 'mpesa:test-tenant {tenant_name}';
    protected $description = 'Test M-Pesa payment for a specific tenant';

    public function handle()
    {
        $tenantName = $this->argument('tenant_name');
        
        $this->info("Testing M-Pesa payment for tenant: {$tenantName}");
        
        // Find the tenant
        $tenant = User::where('name', 'LIKE', "%{$tenantName}%")
            ->role('tenant')
            ->first();
            
        if (!$tenant) {
            $this->error("Tenant '{$tenantName}' not found!");
            $this->line('Available tenants:');
            $tenants = User::role('tenant')->take(10)->get(['id', 'name', 'phone']);
            $this->table(['ID', 'Name', 'Phone'], $tenants->toArray());
            return 1;
        }
        
        $this->info("✓ Found tenant: {$tenant->name} (ID: {$tenant->id})");
        $this->line("Phone: {$tenant->phone}");
        
        // Find an unpaid invoice for this tenant
        $invoice = Invoice::where('tenant_id', $tenant->id)
            ->whereIn('status', ['pending', 'overdue', 'partially_paid'])
            ->first();
            
        if (!$invoice) {
            $this->warn("No unpaid invoices found for {$tenant->name}");
            $this->line('Creating a test invoice...');
            
            // Create a test invoice
            $invoice = Invoice::create([
                'tenant_id' => $tenant->id,
                'landlord_id' => 1, // Assuming landlord ID 1 exists
                'property_id' => 1, // Assuming property ID 1 exists
                'house_id' => 1, // Assuming house ID 1 exists
                'invoice_id' => 'TEST-' . time(),
                'lease_reference' => 'TEST-LEASE-' . time(),
                'amount' => 1000,
                'balance_due' => 1000,
                'status' => 'pending',
                'due_date' => now()->addDays(7),
                'description' => 'Test invoice for M-Pesa payment testing'
            ]);
            
            $this->info("✓ Created test invoice: {$invoice->invoice_id}");
        } else {
            $this->info("✓ Found unpaid invoice: {$invoice->invoice_id}");
        }
        
        $this->line("Invoice Amount: KES {$invoice->balance_due}");
        $this->line("Reference: {$invoice->lease_reference}");
        
        // Test M-Pesa payment
        $this->info('Initiating M-Pesa STK Push...');
        
        $response = MPesaHelper::stkPush(
            $tenant->phone,
            $invoice->balance_due,
            $invoice->lease_reference,
            $tenant->id,
            $invoice->id
        );
        
        if ($response['status'] === 'success') {
            $this->info('✓ M-Pesa STK Push initiated successfully!');
            $this->line("Checkout Request ID: {$response['checkout_request_id']}");
            $this->line("Customer Message: {$response['customer_message']}");
            $this->line('');
            $this->info('The tenant should now receive an STK push on their phone.');
            $this->line('They need to enter their M-Pesa PIN to complete the payment.');
        } else {
            $this->error('✗ M-Pesa STK Push failed!');
            $this->line("Error: {$response['errorMessage']}");
            if (isset($response['error'])) {
                $this->line('Details: ' . json_encode($response['error']));
            }
        }
        
        return 0;
    }
}
