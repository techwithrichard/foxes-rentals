<?php

namespace Tests\Unit;

use App\Services\PaymentGateways\PaymentGatewayManager;
use App\Services\PaymentGateways\MPesaGateway;
use App\Services\PaymentGateways\BankTransferGateway;
use App\Services\PaymentGateways\CashGateway;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\User;
use App\Models\PropertyConsolidated;
use App\Enums\PaymentStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class PaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

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
        
        $this->gatewayManager = new PaymentGatewayManager();
    }

    public function test_can_get_available_gateways()
    {
        $gateways = $this->gatewayManager->getAvailableGateways();

        $this->assertIsArray($gateways);
        $this->assertArrayHasKey('cash', $gateways);
        $this->assertArrayHasKey('mpesa', $gateways);
    }

    public function test_can_get_gateway_by_name()
    {
        $mpesaGateway = $this->gatewayManager->getGateway('mpesa');
        $cashGateway = $this->gatewayManager->getGateway('cash');
        $bankGateway = $this->gatewayManager->getGateway('bank_transfer');

        $this->assertInstanceOf(MPesaGateway::class, $mpesaGateway);
        $this->assertInstanceOf(CashGateway::class, $cashGateway);
        $this->assertInstanceOf(BankTransferGateway::class, $bankGateway);
    }

    public function test_can_get_gateway_for_payment_method()
    {
        $mpesaGateway = $this->gatewayManager->getGatewayForMethod('MPESA_STK');
        $cashGateway = $this->gatewayManager->getGatewayForMethod('CASH');
        $bankGateway = $this->gatewayManager->getGatewayForMethod('BANK_TRANSFER');

        $this->assertInstanceOf(MPesaGateway::class, $mpesaGateway);
        $this->assertInstanceOf(CashGateway::class, $cashGateway);
        $this->assertInstanceOf(BankTransferGateway::class, $bankGateway);
    }

    public function test_can_initialize_cash_payment()
    {
        $paymentData = [
            'amount' => 50000,
            'invoice_id' => $this->invoice->id,
            'payment_method' => 'CASH'
        ];

        $result = $this->gatewayManager->initializePayment($paymentData);

        $this->assertTrue($result['success']);
        $this->assertEquals('cash', $result['gateway']);
        $this->assertArrayHasKey('reference', $result);
        $this->assertStringStartsWith('CASH', $result['reference']);
    }

    public function test_can_initialize_bank_transfer_payment()
    {
        $paymentData = [
            'amount' => 50000,
            'invoice_id' => $this->invoice->id,
            'payment_method' => 'BANK_TRANSFER'
        ];

        $result = $this->gatewayManager->initializePayment($paymentData);

        $this->assertTrue($result['success']);
        $this->assertEquals('bank_transfer', $result['gateway']);
        $this->assertArrayHasKey('reference', $result);
        $this->assertStringStartsWith('BT', $result['reference']);
    }

    public function test_can_process_cash_payment()
    {
        $payment = Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'payment_method' => 'CASH',
            'status' => PaymentStatusEnum::PENDING
        ]);

        $result = $this->gatewayManager->processPayment($payment);

        $this->assertTrue($result['success']);
        $this->assertEquals('completed', $result['status']);
    }

    public function test_can_verify_cash_payment()
    {
        $payment = Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'payment_method' => 'CASH',
            'reference_number' => 'CASH123456789',
            'status' => PaymentStatusEnum::PAID
        ]);

        $result = $this->gatewayManager->verifyPayment('CASH123456789', 'CASH');

        $this->assertTrue($result['success']);
        $this->assertEquals('paid', $result['status']);
        $this->assertEquals(50000, $result['amount']);
    }

    public function test_can_cancel_cash_payment()
    {
        $payment = Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'payment_method' => 'CASH',
            'status' => PaymentStatusEnum::PENDING
        ]);

        $result = $this->gatewayManager->cancelPayment($payment);

        $this->assertTrue($result['success']);
        $this->assertEquals('Payment cancelled successfully', $result['message']);
    }

    public function test_can_refund_cash_payment()
    {
        $payment = Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'payment_method' => 'CASH',
            'amount' => 50000,
            'status' => PaymentStatusEnum::PAID
        ]);

        $result = $this->gatewayManager->refundPayment($payment, 25000);

        $this->assertTrue($result['success']);
        $this->assertEquals('REF123456789', $result['refund_id']);
        $this->assertEquals(25000, $result['amount']);
    }

    public function test_can_get_all_supported_methods()
    {
        $methods = $this->gatewayManager->getAllSupportedMethods();

        $this->assertIsArray($methods);
        $this->assertArrayHasKey('CASH', $methods);
        $this->assertArrayHasKey('MPESA_STK', $methods);
        $this->assertArrayHasKey('BANK_TRANSFER', $methods);
    }

    public function test_can_check_method_support()
    {
        $this->assertTrue($this->gatewayManager->isMethodSupported('CASH'));
        $this->assertTrue($this->gatewayManager->isMethodSupported('MPESA_STK'));
        $this->assertTrue($this->gatewayManager->isMethodSupported('BANK_TRANSFER'));
        $this->assertFalse($this->gatewayManager->isMethodSupported('INVALID_METHOD'));
    }

    public function test_can_get_gateway_statistics()
    {
        $stats = $this->gatewayManager->getGatewayStatistics();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('cash', $stats);
        $this->assertArrayHasKey('mpesa', $stats);
        $this->assertArrayHasKey('bank_transfer', $stats);

        foreach ($stats as $gateway) {
            $this->assertArrayHasKey('name', $gateway);
            $this->assertArrayHasKey('available', $gateway);
            $this->assertArrayHasKey('config', $gateway);
            $this->assertArrayHasKey('methods_count', $gateway);
        }
    }

    public function test_cash_gateway_is_always_available()
    {
        $cashGateway = $this->gatewayManager->getGateway('cash');
        $this->assertTrue($cashGateway->isAvailable());
    }

    public function test_cash_gateway_supports_cash_methods()
    {
        $cashGateway = $this->gatewayManager->getGateway('cash');
        $methods = $cashGateway->getSupportedMethods();

        $this->assertArrayHasKey('CASH', $methods);
        $this->assertArrayHasKey('CASH_OFFICE', $methods);
        $this->assertArrayHasKey('CASH_COLLECTION', $methods);
    }

    public function test_cash_gateway_can_generate_receipt()
    {
        $payment = Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'payment_method' => 'CASH',
            'amount' => 50000,
            'reference_number' => 'CASH123456789',
            'status' => PaymentStatusEnum::PAID,
            'verified_at' => now(),
            'verified_by' => $this->tenant->id
        ]);

        $cashGateway = $this->gatewayManager->getGateway('cash');
        $receipt = $cashGateway->generateReceipt($payment);

        $this->assertIsArray($receipt);
        $this->assertEquals('CASH123456789', $receipt['receipt_number']);
        $this->assertEquals(50000, $receipt['amount']);
        $this->assertEquals('CASH', $receipt['payment_method']);
    }

    public function test_bank_transfer_gateway_can_get_instructions()
    {
        $paymentData = [
            'amount' => 50000,
            'invoice_id' => $this->invoice->id
        ];

        $bankGateway = $this->gatewayManager->getGateway('bank_transfer');
        $instructions = $bankGateway->getTransferInstructions($paymentData);

        $this->assertIsArray($instructions);
        $this->assertArrayHasKey('account_number', $instructions);
        $this->assertArrayHasKey('account_name', $instructions);
        $this->assertArrayHasKey('bank_name', $instructions);
        $this->assertArrayHasKey('reference', $instructions);
        $this->assertArrayHasKey('amount', $instructions);
        $this->assertArrayHasKey('instructions', $instructions);
    }

    public function test_mpesa_gateway_can_format_phone_number()
    {
        $mpesaGateway = $this->gatewayManager->getGateway('mpesa');

        // Test various phone number formats
        $this->assertEquals('254712345678', $mpesaGateway->formatPhoneNumber('0712345678'));
        $this->assertEquals('254712345678', $mpesaGateway->formatPhoneNumber('254712345678'));
        $this->assertEquals('254712345678', $mpesaGateway->formatPhoneNumber('+254712345678'));
    }

    public function test_mpesa_gateway_validates_phone_numbers()
    {
        $mpesaGateway = $this->gatewayManager->getGateway('mpesa');

        $this->assertTrue($mpesaGateway->isValidPhoneNumber('254712345678'));
        $this->assertTrue($mpesaGateway->isValidPhoneNumber('0712345678'));
        $this->assertFalse($mpesaGateway->isValidPhoneNumber('123456789'));
        $this->assertFalse($mpesaGateway->isValidPhoneNumber('invalid'));
    }

    public function test_handles_invalid_gateway_name()
    {
        $gateway = $this->gatewayManager->getGateway('invalid_gateway');
        $this->assertNull($gateway);
    }

    public function test_handles_invalid_payment_method()
    {
        $gateway = $this->gatewayManager->getGatewayForMethod('INVALID_METHOD');
        $this->assertInstanceOf(MPesaGateway::class, $gateway); // Should return default gateway
    }

    public function test_initialization_fails_for_invalid_data()
    {
        $invalidData = [
            'amount' => -100, // Invalid amount
            'payment_method' => 'CASH'
        ];

        $result = $this->gatewayManager->initializePayment($invalidData);

        $this->assertFalse($result['success']);
        $this->assertStringContains('Amount must be greater than 0', $result['error']);
    }
}
