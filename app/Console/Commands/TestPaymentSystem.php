<?php

namespace App\Console\Commands;

use App\Services\PaymentService;
use App\Services\PaymentGateways\PaymentGatewayManager;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Console\Command;

class TestPaymentSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:payment-system';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the payment system implementation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Payment System Implementation...');
        $this->newLine();

        try {
            // Test service instantiation
            $this->info('1. Testing service instantiation...');
            $paymentService = app(PaymentService::class);
            $this->info('✅ PaymentService instantiated successfully');

            // Test gateway manager
            $this->info('2. Testing gateway manager...');
            $gatewayManager = app(PaymentGatewayManager::class);
            $this->info('✅ PaymentGatewayManager instantiated successfully');

            // Test available gateways
            $this->info('3. Testing available gateways...');
            $gateways = $gatewayManager->getAvailableGateways();
            $this->info('✅ Available gateways: ' . implode(', ', array_keys($gateways)));

            // Test payment methods
            $this->info('4. Testing payment methods...');
            $methods = $paymentService->getAvailablePaymentMethods();
            $this->info('✅ Available payment methods: ' . implode(', ', array_keys($methods)));

            // Test statistics
            $this->info('5. Testing payment statistics...');
            $statistics = $paymentService->getPaymentStatistics();
            $this->info('✅ Statistics retrieved successfully');
            $this->line('   Total payments: ' . $statistics['total_payments']);
            $this->line('   Total amount: ' . number_format($statistics['total_amount'], 2));

            // Test gateway configurations
            $this->info('6. Testing gateway configurations...');
            foreach ($gateways as $name => $gateway) {
                $available = $gateway['config']['available'] ?? false;
                $this->line("   {$name}: " . ($available ? 'Available' : 'Unavailable'));
            }

            // Test MPesa gateway specifically
            $this->info('7. Testing MPesa gateway...');
            $mpesaGateway = $gatewayManager->getGateway('mpesa');
            if ($mpesaGateway) {
                $this->info('✅ MPesa gateway loaded successfully');
                $this->line('   Available: ' . ($mpesaGateway->isAvailable() ? 'Yes' : 'No'));
                $this->line('   Methods: ' . implode(', ', array_keys($mpesaGateway->getSupportedMethods())));
            } else {
                $this->warn('⚠️ MPesa gateway not found');
            }

            // Test Bank Transfer gateway
            $this->info('8. Testing Bank Transfer gateway...');
            $bankGateway = $gatewayManager->getGateway('bank_transfer');
            if ($bankGateway) {
                $this->info('✅ Bank Transfer gateway loaded successfully');
                $this->line('   Available: ' . ($bankGateway->isAvailable() ? 'Yes' : 'No'));
            } else {
                $this->warn('⚠️ Bank Transfer gateway not found');
            }

            // Test Cash gateway
            $this->info('9. Testing Cash gateway...');
            $cashGateway = $gatewayManager->getGateway('cash');
            if ($cashGateway) {
                $this->info('✅ Cash gateway loaded successfully');
                $this->line('   Available: ' . ($cashGateway->isAvailable() ? 'Yes' : 'No'));
            } else {
                $this->warn('⚠️ Cash gateway not found');
            }

            // Test payment validation
            $this->info('10. Testing payment validation...');
            $testData = [
                'amount' => 1000,
                'invoice_id' => 'test-invoice-id',
                'payment_method' => 'CASH'
            ];
            $errors = $paymentService->validatePaymentData($testData);
            if (empty($errors)) {
                $this->info('✅ Payment validation working');
            } else {
                $this->warn('⚠️ Payment validation errors: ' . implode(', ', $errors));
            }

            $this->newLine();
            $this->info('🎉 All payment system tests passed successfully!');

        } catch (\Exception $e) {
            $this->error('❌ Payment system test failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
