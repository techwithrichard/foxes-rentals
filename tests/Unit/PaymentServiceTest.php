<?php

namespace Tests\Unit;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\User;
use App\Models\PropertyConsolidated;
use App\Services\PaymentService;
use App\Services\PaymentGateways\PaymentGatewayManager;
use App\Enums\PaymentStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $paymentService;
    protected $gatewayManager;
    protected $invoice;
    protected $tenant;
    protected $property;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->property = PropertyConsolidated::factory()->create();
        $this->tenant = User::factory()->create();
        
        $this->invoice = Invoice::factory()->create([
            'tenant_id' => $this->tenant->id,
            'property_id' => $this->property->id,
            'amount' => 50000,
            'status' => PaymentStatusEnum::PENDING
        ]);
        
        // Mock the gateway manager
        $this->gatewayManager = Mockery::mock(PaymentGatewayManager::class);
        $this->paymentService = new PaymentService($this->gatewayManager);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_can_create_cash_payment()
    {
        $paymentData = [
            'amount' => 50000,
            'invoice_id' => $this->invoice->id,
            'payment_method' => 'CASH',
            'paid_at' => now(),
        ];

        $gatewayResponse = [
            'success' => true,
            'gateway' => 'cash',
            'reference' => 'CASH123456789',
            'message' => 'Cash payment initialized',
            'data' => ['reference' => 'CASH123456789']
        ];

        $this->gatewayManager
            ->shouldReceive('initializePayment')
            ->once()
            ->with($paymentData)
            ->andReturn($gatewayResponse);

        $result = $this->paymentService->createPayment($paymentData);

        $this->assertTrue($result['success']);
        $this->assertInstanceOf(Payment::class, $result['payment']);
        $this->assertEquals('CASH', $result['payment']->payment_method);
        $this->assertEquals(50000, $result['payment']->amount);
    }

    public function test_can_create_mpesa_payment()
    {
        $paymentData = [
            'amount' => 50000,
            'invoice_id' => $this->invoice->id,
            'payment_method' => 'MPESA_STK',
            'phone' => '254712345678',
        ];

        $gatewayResponse = [
            'success' => true,
            'gateway' => 'mpesa_stk',
            'reference' => 'STK123456789',
            'message' => 'STK Push request sent',
            'data' => ['checkout_request_id' => 'STK123456789']
        ];

        $this->gatewayManager
            ->shouldReceive('initializePayment')
            ->once()
            ->with($paymentData)
            ->andReturn($gatewayResponse);

        $result = $this->paymentService->createPayment($paymentData);

        $this->assertTrue($result['success']);
        $this->assertInstanceOf(Payment::class, $result['payment']);
        $this->assertEquals('MPESA_STK', $result['payment']->payment_method);
        $this->assertEquals('STK123456789', $result['payment']->reference_number);
    }

    public function test_can_process_payment()
    {
        $payment = Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'payment_method' => 'CASH',
            'status' => PaymentStatusEnum::PENDING
        ]);

        $gatewayResponse = [
            'success' => true,
            'status' => 'completed',
            'message' => 'Payment processed successfully'
        ];

        $this->gatewayManager
            ->shouldReceive('processPayment')
            ->once()
            ->with($payment)
            ->andReturn($gatewayResponse);

        $result = $this->paymentService->processPayment($payment);

        $this->assertTrue($result['success']);
        $this->assertEquals('completed', $result['status']);
    }

    public function test_can_verify_payment()
    {
        $payment = Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'payment_method' => 'MPESA_STK',
            'status' => PaymentStatusEnum::PENDING,
            'reference_number' => 'STK123456789'
        ]);

        $gatewayResponse = [
            'success' => true,
            'status' => 'completed',
            'amount' => 50000,
            'reference' => 'STK123456789'
        ];

        $this->gatewayManager
            ->shouldReceive('verifyPayment')
            ->once()
            ->with('STK123456789', 'MPESA_STK')
            ->andReturn($gatewayResponse);

        $result = $this->paymentService->verifyPayment($payment);

        $this->assertTrue($result['success']);
        $this->assertEquals('completed', $result['status']);
    }

    public function test_can_cancel_payment()
    {
        $payment = Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'payment_method' => 'MPESA_STK',
            'status' => PaymentStatusEnum::PENDING
        ]);

        $gatewayResponse = [
            'success' => true,
            'message' => 'Payment cancelled successfully'
        ];

        $this->gatewayManager
            ->shouldReceive('cancelPayment')
            ->once()
            ->with($payment)
            ->andReturn($gatewayResponse);

        $result = $this->paymentService->cancelPayment($payment);

        $this->assertTrue($result['success']);
        $this->assertEquals('Payment cancelled successfully', $result['message']);
    }

    public function test_can_refund_payment()
    {
        $payment = Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'payment_method' => 'CASH',
            'amount' => 50000,
            'status' => PaymentStatusEnum::PAID
        ]);

        $gatewayResponse = [
            'success' => true,
            'refund_id' => 'REF123456789',
            'amount' => 50000,
            'message' => 'Refund processed successfully'
        ];

        $this->gatewayManager
            ->shouldReceive('refundPayment')
            ->once()
            ->with($payment, null)
            ->andReturn($gatewayResponse);

        $result = $this->paymentService->refundPayment($payment);

        $this->assertTrue($result['success']);
        $this->assertEquals('REF123456789', $result['refund_id']);
    }

    public function test_can_get_payment_status()
    {
        $payment = Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'payment_method' => 'MPESA_STK',
            'reference_number' => 'STK123456789'
        ]);

        $gatewayResponse = [
            'success' => true,
            'status' => 'completed',
            'amount' => 50000
        ];

        $this->gatewayManager
            ->shouldReceive('getPaymentStatus')
            ->once()
            ->with('STK123456789', 'MPESA_STK')
            ->andReturn($gatewayResponse);

        $result = $this->paymentService->getPaymentStatus($payment);

        $this->assertTrue($result['success']);
        $this->assertEquals('completed', $result['status']);
    }

    public function test_can_get_available_payment_methods()
    {
        $methods = [
            'CASH' => 'Cash Payment',
            'MPESA_STK' => 'M-PESA STK Push',
            'BANK_TRANSFER' => 'Bank Transfer'
        ];

        $this->gatewayManager
            ->shouldReceive('getAllSupportedMethods')
            ->once()
            ->andReturn($methods);

        $result = $this->paymentService->getAvailablePaymentMethods();

        $this->assertEquals($methods, $result);
    }

    public function test_can_get_available_gateways()
    {
        $gateways = [
            'cash' => [
                'name' => 'cash',
                'config' => ['available' => true],
                'methods' => ['CASH' => 'Cash Payment']
            ],
            'mpesa' => [
                'name' => 'mpesa',
                'config' => ['available' => true],
                'methods' => ['MPESA_STK' => 'M-PESA STK Push']
            ]
        ];

        $this->gatewayManager
            ->shouldReceive('getAvailableGateways')
            ->once()
            ->andReturn($gateways);

        $result = $this->paymentService->getAvailableGateways();

        $this->assertEquals($gateways, $result);
    }

    public function test_can_validate_payment_data()
    {
        $validData = [
            'amount' => 50000,
            'invoice_id' => $this->invoice->id,
            'payment_method' => 'CASH'
        ];

        $this->gatewayManager
            ->shouldReceive('isMethodSupported')
            ->once()
            ->with('CASH')
            ->andReturn(true);

        $errors = $this->paymentService->validatePaymentData($validData);

        $this->assertEmpty($errors);
    }

    public function test_validation_fails_for_invalid_data()
    {
        $invalidData = [
            'amount' => -100, // Invalid amount
            'payment_method' => 'INVALID_METHOD'
        ];

        $this->gatewayManager
            ->shouldReceive('isMethodSupported')
            ->once()
            ->with('INVALID_METHOD')
            ->andReturn(false);

        $errors = $this->paymentService->validatePaymentData($invalidData);

        $this->assertNotEmpty($errors);
        $this->assertContains('Amount must be greater than 0', $errors);
        $this->assertContains('Invoice ID is required', $errors);
        $this->assertContains('Payment method not supported', $errors);
    }

    public function test_can_get_payment_statistics()
    {
        // Create test payments
        Payment::factory()->create([
            'amount' => 50000,
            'status' => PaymentStatusEnum::PAID,
            'payment_method' => 'CASH'
        ]);

        Payment::factory()->create([
            'amount' => 30000,
            'status' => PaymentStatusEnum::PENDING,
            'payment_method' => 'MPESA_STK'
        ]);

        $gateways = [
            'cash' => ['name' => 'cash', 'config' => ['available' => true], 'methods' => []]
        ];

        $this->gatewayManager
            ->shouldReceive('getAvailableGateways')
            ->once()
            ->andReturn($gateways);

        $statistics = $this->paymentService->getPaymentStatistics();

        $this->assertEquals(2, $statistics['total_payments']);
        $this->assertEquals(50000, $statistics['total_amount']);
        $this->assertEquals(1, $statistics['pending_payments']);
        $this->assertArrayHasKey('payment_methods', $statistics);
        $this->assertArrayHasKey('available_gateways', $statistics);
    }

    public function test_can_handle_stk_callback()
    {
        $callbackData = [
            'Body' => [
                'stkCallback' => [
                    'CheckoutRequestID' => 'STK123456789',
                    'ResultCode' => 0,
                    'ResultDesc' => 'Success'
                ]
            ]
        ];

        $gatewayResponse = [
            'success' => true,
            'result_code' => 0,
            'result_desc' => 'Success',
            'status' => 'completed'
        ];

        $this->gatewayManager
            ->shouldReceive('getGateway')
            ->once()
            ->with('mpesa')
            ->andReturn(Mockery::mock()->shouldReceive('handleStkCallback')->once()->with($callbackData)->andReturn($gatewayResponse)->getMock());

        $result = $this->paymentService->handleStkCallback($callbackData);

        $this->assertTrue($result['success']);
        $this->assertEquals('completed', $result['status']);
    }

    public function test_handles_gateway_initialization_failure()
    {
        $paymentData = [
            'amount' => 50000,
            'invoice_id' => $this->invoice->id,
            'payment_method' => 'CASH'
        ];

        $gatewayResponse = [
            'success' => false,
            'error' => 'Gateway initialization failed'
        ];

        $this->gatewayManager
            ->shouldReceive('initializePayment')
            ->once()
            ->with($paymentData)
            ->andReturn($gatewayResponse);

        $result = $this->paymentService->createPayment($paymentData);

        $this->assertFalse($result['success']);
        $this->assertEquals('Gateway initialization failed', $result['error']);
    }
}
