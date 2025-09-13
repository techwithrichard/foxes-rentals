<?php

namespace Tests\Feature;

use App\Models\StkRequest;
use App\Models\C2bRequest;
use App\Models\User;
use App\Models\Invoice;
use App\Services\MPesaHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MpesaPaymentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_mpesa_configuration_is_loaded()
    {
        $this->assertNotNull(config('mpesa.consumer_key'));
        $this->assertNotNull(config('mpesa.consumer_secret'));
        $this->assertNotNull(config('mpesa.business_shortcode'));
        $this->assertNotNull(config('mpesa.paybill'));
        $this->assertNotNull(config('mpesa.env'));
        $this->assertNotNull(config('mpesa.stk_callback_url'));
    }

    public function test_stk_request_model_has_relationships()
    {
        $stkRequest = new StkRequest();
        
        $this->assertTrue(method_exists($stkRequest, 'user'));
        $this->assertTrue(method_exists($stkRequest, 'invoice'));
    }

    public function test_c2b_request_model_exists()
    {
        $c2bRequest = new C2bRequest();
        $this->assertInstanceOf(C2bRequest::class, $c2bRequest);
    }

    public function test_mpesa_helper_can_generate_access_token()
    {
        // This test will only work if valid credentials are provided
        try {
            $token = MPesaHelper::generateAccessToken();
            $this->assertIsString($token);
            $this->assertNotEmpty($token);
        } catch (\Exception $e) {
            $this->markTestSkipped('MPesa credentials not configured: ' . $e->getMessage());
        }
    }

    public function test_stk_request_can_be_created_with_user_and_invoice()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'phone' => '254712345678'
        ]);
        
        $invoice = Invoice::create([
            'tenant_id' => $user->id,
            'property_id' => null,
            'house_id' => null,
            'amount' => 1000.00,
            'paid_amount' => 0.00,
            'status' => 'pending'
        ]);

        $stkRequest = StkRequest::create([
            'user_id' => $user->id,
            'invoice_id' => $invoice->id,
            'phone' => '254712345678',
            'amount' => 1000.00,
            'reference' => 'TEST-REF-001',
            'description' => 'Test Payment',
            'MerchantRequestID' => 'test-merchant-id',
            'CheckoutRequestID' => 'test-checkout-id',
            'status' => 'Request Sent'
        ]);

        $this->assertInstanceOf(StkRequest::class, $stkRequest);
        $this->assertEquals($user->id, $stkRequest->user_id);
        $this->assertEquals($invoice->id, $stkRequest->invoice_id);
        $this->assertEquals('Request Sent', $stkRequest->status);
    }

    public function test_stk_request_relationships_work()
    {
        $user = User::create([
            'name' => 'Test User 2',
            'email' => 'test2@example.com',
            'password' => bcrypt('password'),
            'phone' => '254712345679'
        ]);
        
        $invoice = Invoice::create([
            'tenant_id' => $user->id,
            'property_id' => null,
            'house_id' => null,
            'amount' => 2000.00,
            'paid_amount' => 0.00,
            'status' => 'pending'
        ]);

        $stkRequest = StkRequest::create([
            'user_id' => $user->id,
            'invoice_id' => $invoice->id,
            'phone' => '254712345678',
            'amount' => 1000.00,
            'reference' => 'TEST-REF-002',
            'description' => 'Test Payment',
            'MerchantRequestID' => 'test-merchant-id-2',
            'CheckoutRequestID' => 'test-checkout-id-2',
            'status' => 'Request Sent'
        ]);

        $this->assertInstanceOf(User::class, $stkRequest->user);
        $this->assertEquals($user->id, $stkRequest->user->id);
        
        $this->assertInstanceOf(Invoice::class, $stkRequest->invoice);
        $this->assertEquals($invoice->id, $stkRequest->invoice->id);
    }

    public function test_c2b_request_can_be_created()
    {
        $c2bRequest = C2bRequest::create([
            'TransactionType' => 'Pay Bill',
            'TransID' => 'TEST-TRANS-001',
            'TransTime' => '20231201120000',
            'TransAmount' => '1000.00',
            'BusinessShortCode' => '600986',
            'BillRefNumber' => 'TEST-BILL-001',
            'InvoiceNumber' => 'INV-001',
            'OrgAccountBalance' => '50000.00',
            'ThirdPartyTransID' => 'TP-001',
            'MSISDN' => '254712345678',
            'FirstName' => 'John'
        ]);

        $this->assertInstanceOf(C2bRequest::class, $c2bRequest);
        $this->assertEquals('TEST-TRANS-001', $c2bRequest->TransID);
        $this->assertEquals('1000.00', $c2bRequest->TransAmount);
    }

    public function test_mpesa_helper_stk_push_method_signature()
    {
        $reflection = new \ReflectionMethod(MPesaHelper::class, 'stkPush');
        $parameters = $reflection->getParameters();
        
        $this->assertCount(5, $parameters);
        $this->assertEquals('phone', $parameters[0]->getName());
        $this->assertEquals('amount', $parameters[1]->getName());
        $this->assertEquals('reference', $parameters[2]->getName());
        $this->assertEquals('userId', $parameters[3]->getName());
        $this->assertEquals('invoiceId', $parameters[4]->getName());
    }

    public function test_mpesa_helper_ip_validation()
    {
        $request = new \Illuminate\Http\Request();
        
        // Test with a whitelisted IP
        $whitelistedIPs = config('mpesa.whitelisted_ips');
        if (!empty($whitelistedIPs)) {
            $request->server->set('REMOTE_ADDR', $whitelistedIPs[0]);
            $this->assertTrue(MPesaHelper::ipIsFromSafaricom($request));
        }
        
        // Test with a non-whitelisted IP
        $request->server->set('REMOTE_ADDR', '192.168.1.1');
        $this->assertFalse(MPesaHelper::ipIsFromSafaricom($request));
    }
}