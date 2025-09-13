<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Property;
use App\Models\House;
use App\Models\Lease;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\C2bRequest;
use App\Models\StkRequest;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear tables in reverse dependency order
        Payment::truncate();
        Invoice::truncate();
        Lease::truncate();
        House::truncate();
        Property::truncate();
        User::truncate();
        C2bRequest::truncate();
        StkRequest::truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create Admin User
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@foxesrental.com',
            'password' => Hash::make('admin123'),
            'phone' => '+254700000000',
            'role' => 'admin',
                'email_verified_at' => now(),
        ]);

        // Create Landlords
        $landlord1 = User::create([
            'name' => 'John Kamau',
            'email' => 'john.kamau@email.com',
            'password' => Hash::make('landlord123'),
            'phone' => '+254712345678',
            'role' => 'landlord',
                'email_verified_at' => now(),
        ]);

        $landlord2 = User::create([
            'name' => 'Mary Wanjiku',
            'email' => 'mary.wanjiku@email.com',
            'password' => Hash::make('landlord123'),
            'phone' => '+254723456789',
            'role' => 'landlord',
            'email_verified_at' => now(),
        ]);

        // Create Tenants
        $tenant1 = User::create([
            'name' => 'Peter Mwangi',
            'email' => 'peter.mwangi@email.com',
            'password' => Hash::make('tenant123'),
            'phone' => '+254734567890',
            'role' => 'tenant',
            'email_verified_at' => now(),
        ]);

        $tenant2 = User::create([
            'name' => 'Grace Akinyi',
            'email' => 'grace.akinyi@email.com',
            'password' => Hash::make('tenant123'),
            'phone' => '+254745678901',
            'role' => 'tenant',
            'email_verified_at' => now(),
        ]);

        $tenant3 = User::create([
            'name' => 'David Ochieng',
            'email' => 'david.ochieng@email.com',
            'password' => Hash::make('tenant123'),
            'phone' => '+254756789012',
            'role' => 'tenant',
            'email_verified_at' => now(),
        ]);

        // Create Properties
        $property1 = Property::create([
            'name' => 'Sunset Gardens Apartments',
            'description' => 'Modern apartments in Westlands with great amenities',
            'landlord_id' => $landlord1->id,
            'commission' => 10.0,
            'status' => 'active',
            'address' => 'Westlands, Nairobi',
        ]);

        $property2 = Property::create([
            'name' => 'Greenview Estate',
            'description' => 'Spacious family houses in Karen',
            'landlord_id' => $landlord2->id,
            'commission' => 8.5,
            'status' => 'active',
            'address' => 'Karen, Nairobi',
        ]);

        // Create Houses
        $house1 = House::create([
            'property_id' => $property1->id,
            'house_number' => 'A1',
            'type' => '1 Bedroom',
            'rent_amount' => 25000,
            'deposit_amount' => 50000,
            'status' => 'occupied',
            'description' => 'Furnished 1 bedroom apartment with balcony',
        ]);

        $house2 = House::create([
            'property_id' => $property1->id,
            'house_number' => 'A2',
            'type' => '2 Bedroom',
            'rent_amount' => 35000,
            'deposit_amount' => 70000,
            'status' => 'occupied',
            'description' => 'Spacious 2 bedroom apartment with parking',
        ]);

        $house3 = House::create([
            'property_id' => $property2->id,
            'house_number' => 'B1',
            'type' => '3 Bedroom',
            'rent_amount' => 45000,
            'deposit_amount' => 90000,
            'status' => 'occupied',
            'description' => 'Family house with garden and parking',
        ]);

        // Create Leases
        $lease1 = Lease::create([
            'tenant_id' => $tenant1->id,
            'house_id' => $house1->id,
            'property_id' => $property1->id,
            'landlord_id' => $landlord1->id,
            'start_date' => Carbon::now()->subMonths(6),
            'end_date' => Carbon::now()->addMonths(6),
            'rent_amount' => 25000,
            'deposit_amount' => 50000,
            'commission' => 10.0,
            'status' => 'active',
            'reference' => 'LSE-001-2024',
        ]);

        $lease2 = Lease::create([
            'tenant_id' => $tenant2->id,
            'house_id' => $house2->id,
            'property_id' => $property1->id,
            'landlord_id' => $landlord1->id,
            'start_date' => Carbon::now()->subMonths(3),
            'end_date' => Carbon::now()->addMonths(9),
            'rent_amount' => 35000,
            'deposit_amount' => 70000,
            'commission' => 10.0,
            'status' => 'active',
            'reference' => 'LSE-002-2024',
        ]);

        $lease3 = Lease::create([
            'tenant_id' => $tenant3->id,
            'house_id' => $house3->id,
            'property_id' => $property2->id,
            'landlord_id' => $landlord2->id,
            'start_date' => Carbon::now()->subMonths(1),
            'end_date' => Carbon::now()->addMonths(11),
            'rent_amount' => 45000,
            'deposit_amount' => 90000,
            'commission' => 8.5,
            'status' => 'active',
            'reference' => 'LSE-003-2024',
        ]);

        // Create Sample Invoices
        $invoice1 = Invoice::create([
            'invoice_id' => 'INV-001-2024',
            'tenant_id' => $tenant1->id,
            'landlord_id' => $landlord1->id,
            'property_id' => $property1->id,
            'house_id' => $house1->id,
            'lease_id' => $lease1->id,
            'amount' => 25000,
            'due_date' => Carbon::now()->addDays(15),
            'status' => 'pending',
            'type' => 'rent',
            'period_start' => Carbon::now()->startOfMonth(),
            'period_end' => Carbon::now()->endOfMonth(),
            'commission' => 10.0,
        ]);

        $invoice2 = Invoice::create([
            'invoice_id' => 'INV-002-2024',
            'tenant_id' => $tenant2->id,
            'landlord_id' => $landlord1->id,
            'property_id' => $property1->id,
            'house_id' => $house2->id,
            'lease_id' => $lease2->id,
            'amount' => 35000,
            'due_date' => Carbon::now()->addDays(10),
            'status' => 'pending',
            'type' => 'rent',
            'period_start' => Carbon::now()->startOfMonth(),
            'period_end' => Carbon::now()->endOfMonth(),
            'commission' => 10.0,
        ]);

        $invoice3 = Invoice::create([
            'invoice_id' => 'INV-003-2024',
            'tenant_id' => $tenant3->id,
            'landlord_id' => $landlord2->id,
            'property_id' => $property2->id,
            'house_id' => $house3->id,
            'lease_id' => $lease3->id,
            'amount' => 45000,
            'due_date' => Carbon::now()->addDays(5),
            'status' => 'pending',
            'type' => 'rent',
            'period_start' => Carbon::now()->startOfMonth(),
            'period_end' => Carbon::now()->endOfMonth(),
            'commission' => 8.5,
        ]);

        // Create Sample Payments
        $payment1 = Payment::create([
            'amount' => 25000,
            'paid_at' => Carbon::now()->subDays(5),
            'payment_method' => 'MPESA STK',
            'reference_number' => 'NEF61A5XYZ',
            'tenant_id' => $tenant1->id,
            'invoice_id' => $invoice1->id,
            'recorded_by' => $admin->id,
            'landlord_id' => $landlord1->id,
            'commission' => 10.0,
            'property_id' => $property1->id,
            'house_id' => $house1->id,
            'status' => 'paid',
        ]);

        // Create Sample MPesa Requests
        $c2bRequest1 = C2bRequest::create([
            'TransactionType' => 'Pay Bill',
            'TransID' => 'NEF61A5XYZ',
            'TransTime' => Carbon::now()->subDays(5)->format('YmdHis'),
            'TransAmount' => '25000',
            'BusinessShortCode' => '174379',
            'BillRefNumber' => 'INV-001-2024',
            'InvoiceNumber' => 'INV-001-2024',
            'OrgAccountBalance' => '50000',
            'ThirdPartyTransID' => null,
            'MSISDN' => '254734567890',
            'FirstName' => 'PETER',
            'reconciliation_status' => 1, // Reconciled
        ]);

        $stkRequest1 = StkRequest::create([
            'phone' => '254745678901',
            'amount' => 35000,
            'reference' => 'INV-002-2024',
            'description' => 'Rent Payment',
            'MerchantRequestID' => 'ws_CO_09012024123456789',
            'CheckoutRequestID' => 'ws_CO_09012024123456789',
            'status' => 'requested',
            'MpesaReceiptNumber' => null,
            'ResultDesc' => null,
            'TransactionDate' => null,
            'user_id' => $tenant2->id,
            'invoice_id' => $invoice2->id,
            'detailed_status' => 'Request Sent',
        ]);

        // Mark invoice 1 as paid
        $invoice1->update([
            'status' => 'paid',
            'paid_at' => Carbon::now()->subDays(5),
        ]);

        $this->command->info('Sample data created successfully!');
        $this->command->info('Admin: admin@foxesrental.com / admin123');
        $this->command->info('Landlord: john.kamau@email.com / landlord123');
        $this->command->info('Tenant: peter.mwangi@email.com / tenant123');
        $this->command->info('Total Records Created:');
        $this->command->info('- Users: 5 (1 Admin, 2 Landlords, 3 Tenants)');
        $this->command->info('- Properties: 2');
        $this->command->info('- Houses: 3');
        $this->command->info('- Leases: 3');
        $this->command->info('- Invoices: 3 (2 pending, 1 paid)');
        $this->command->info('- Payments: 1');
        $this->command->info('- MPesa Requests: 2');
    }
}