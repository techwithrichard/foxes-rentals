<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\House;
use App\Models\Lease;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\C2bRequest;
use App\Models\StkRequest;
use Carbon\Carbon;

class AddRemainingDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Adding remaining sample data...');
        
        try {
            // Get existing users
            $admin = \App\Models\User::where('email', 'admin@foxesrental.com')->first();
            $landlord1 = \App\Models\User::where('email', 'john.kamau@email.com')->first();
            $landlord2 = \App\Models\User::where('email', 'mary.wanjiku@email.com')->first();
            $tenant1 = \App\Models\User::where('email', 'peter.mwangi@email.com')->first();
            $tenant2 = \App\Models\User::where('email', 'grace.akinyi@email.com')->first();
            $tenant3 = \App\Models\User::where('email', 'david.ochieng@email.com')->first();
            
            if (!$admin || !$landlord1 || !$landlord2 || !$tenant1 || !$tenant2 || !$tenant3) {
                $this->command->error('Required users not found. Please run the user seeder first.');
                return;
            }
            
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
            
            $this->command->info('Properties created');
            
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
            
            $this->command->info('Houses created');
            
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
            
            $this->command->info('Leases created');
            
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
            
            $this->command->info('Invoices created');
            
            // Create Sample Payment
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
            
            $this->command->info('All sample data created successfully!');
            
        } catch (\Exception $e) {
            $this->command->error('Error creating remaining data: ' . $e->getMessage());
        }
    }
}
