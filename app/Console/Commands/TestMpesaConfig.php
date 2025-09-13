<?php

namespace App\Console\Commands;

use App\Services\MPesaHelper;
use Illuminate\Console\Command;

class TestMpesaConfig extends Command
{
    protected $signature = 'mpesa:test-config';
    protected $description = 'Test M-Pesa configuration and connection';

    public function handle()
    {
        $this->info('Testing M-Pesa Configuration...');
        
        // Test access token generation
        $this->info('1. Testing access token generation...');
        try {
            $token = MPesaHelper::generateAccessToken();
            $this->info('✓ Access token generated successfully');
            $this->line('Token: ' . substr($token, 0, 20) . '...');
            
            // Validate token format
            if (strlen($token) < 50) {
                $this->warn('⚠ Token seems too short, might be invalid');
            }
        } catch (\Exception $e) {
            $this->error('✗ Failed to generate access token: ' . $e->getMessage());
            $this->line('This usually means invalid consumer key/secret');
            return 1;
        }

        // Display current configuration
        $this->info('2. Current M-Pesa Configuration:');
        $this->table(['Setting', 'Value'], [
            ['Environment', config('mpesa.env')],
            ['Business Shortcode', config('mpesa.business_shortcode')],
            ['Paybill', config('mpesa.paybill')],
            ['Consumer Key', substr(config('mpesa.consumer_key'), 0, 10) . '...'],
            ['Consumer Secret', substr(config('mpesa.consumer_secret'), 0, 10) . '...'],
            ['Passkey', substr(config('mpesa.passkey'), 0, 10) . '...'],
            ['STK Callback URL', config('mpesa.stk_callback_url')],
        ]);

        // Test STK push with minimal amount (1 KES)
        $this->info('3. Testing STK Push (1 KES)...');
        $testPhone = '254700000000'; // Test phone number for sandbox
        $testAmount = 1;
        $testReference = 'TEST-' . time();
        
        try {
            $response = MPesaHelper::stkPush($testPhone, $testAmount, $testReference);
            
            if ($response['status'] === 'success') {
                $this->info('✓ STK Push initiated successfully');
                $this->line('Checkout Request ID: ' . $response['checkout_request_id']);
                $this->line('Customer Message: ' . $response['customer_message']);
            } else {
                $this->error('✗ STK Push failed: ' . $response['errorMessage']);
                if (isset($response['error'])) {
                    $this->line('Error details: ' . json_encode($response['error']));
                }
            }
        } catch (\Exception $e) {
            $this->error('✗ STK Push exception: ' . $e->getMessage());
        }

        $this->info('M-Pesa configuration test completed.');
        return 0;
    }
}
