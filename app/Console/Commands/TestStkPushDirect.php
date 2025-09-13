<?php

namespace App\Console\Commands;

use App\Services\MPesaHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class TestStkPushDirect extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:stk-direct {--phone=254720691181} {--amount=100}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test STK Push directly - you will see M-PESA popup on your phone';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phone = $this->option('phone');
        $amount = $this->option('amount');
        
        $this->info("📱 TESTING STK PUSH DIRECTLY TO YOUR PHONE!");
        $this->info("Phone: {$phone}");
        $this->info("Amount: Ksh {$amount}");
        $this->warn("⚠️  CHECK YOUR PHONE NOW - M-PESA POPUP SHOULD APPEAR!");

        // Check M-PESA configuration
        $this->info("");
        $this->info("🔧 M-PESA Configuration:");
        $this->info("Environment: " . config('mpesa.env'));
        $this->info("Business Shortcode: " . config('mpesa.business_shortcode'));
        $this->info("Paybill: " . config('mpesa.paybill'));
        $this->info("Consumer Key: " . (config('mpesa.consumer_key') ? 'Set' : 'Not Set'));
        $this->info("Consumer Secret: " . (config('mpesa.consumer_secret') ? 'Set' : 'Not Set'));
        $this->info("Passkey: " . (config('mpesa.passkey') ? 'Set' : 'Not Set'));

        try {
            // Generate access token
            $this->info("");
            $this->info("🔑 Generating access token...");
            $accessToken = MPesaHelper::generateAccessToken();
            $this->info("Access token: " . substr($accessToken, 0, 20) . "...");

            // Prepare STK Push request
            $this->info("");
            $this->info("📡 Preparing STK Push request...");
            
            $url = config('mpesa.env') == 'sandbox' ?
                'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest' :
                'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
                
            $businessShortcode = config('mpesa.business_shortcode');
            $passkey = config('mpesa.passkey');
            $timestamp = Carbon::now()->format('YmdHis');
            $password = base64_encode($businessShortcode . $passkey . $timestamp);
            $reference = 'TEST' . time();
            $callbackUrl = config('mpesa.stk_callback_url');

            $this->info("URL: {$url}");
            $this->info("Business Shortcode: {$businessShortcode}");
            $this->info("Timestamp: {$timestamp}");
            $this->info("Reference: {$reference}");
            $this->info("Callback URL: {$callbackUrl}");

            // Make the request
            $this->info("");
            $this->info("🚀 Sending STK Push request...");
            
            $response = Http::withToken($accessToken)->post($url, [
                'BusinessShortCode' => $businessShortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => $amount,
                'PartyA' => $phone,
                'PartyB' => $businessShortcode,
                'PhoneNumber' => $phone,
                'CallBackURL' => $callbackUrl,
                'AccountReference' => $reference,
                'TransactionDesc' => 'Rent Payment Test'
            ]);

            $this->info("Response Status: " . $response->status());
            $this->info("Response Body: " . $response->body());

            $responseData = $response->json();
            
            if ($response->successful() && isset($responseData['ResponseCode']) && $responseData['ResponseCode'] == '0') {
                $this->info("");
                $this->info("✅ STK PUSH SUCCESSFUL!");
                $this->info("📱 CHECK YOUR PHONE NOW!");
                $this->info("Checkout Request ID: " . ($responseData['CheckoutRequestID'] ?? 'N/A'));
                $this->info("Customer Message: " . ($responseData['CustomerMessage'] ?? 'N/A'));
                
                $this->info("");
                $this->info("📱 WHAT TO DO ON YOUR PHONE:");
                $this->info("1. Look for M-PESA popup notification");
                $this->info("2. Tap on the notification");
                $this->info("3. Enter your M-PESA PIN");
                $this->info("4. Confirm the payment");
                $this->info("5. Wait for confirmation SMS");
                
            } else {
                $this->error("");
                $this->error("❌ STK PUSH FAILED!");
                $this->error("Response Code: " . ($responseData['ResponseCode'] ?? 'Unknown'));
                $this->error("Error Message: " . ($responseData['errorMessage'] ?? 'Unknown'));
                $this->error("Customer Message: " . ($responseData['CustomerMessage'] ?? 'Unknown'));
            }

        } catch (\Exception $e) {
            $this->error("");
            $this->error("❌ ERROR: " . $e->getMessage());
            $this->error("File: " . $e->getFile() . ":" . $e->getLine());
        }

        $this->info("");
        $this->info("🔍 To monitor for the payment callback:");
        $this->info("php artisan track:awesome-tenant-payments --watch");

        return 0;
    }
}

