<?php

namespace App\Services;

use App\Models\StkRequest;
use App\Models\STKRequests;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

class MPesaHelper
{
    //method to generate access token
    public static function generateAccessToken()
    {

        $consumer_key = config('mpesa.consumer_key');
        $consumer_secret = config('mpesa.consumer_secret');

        $url = config('mpesa.env') == 'sandbox' ?
            'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials' :
            'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

//        $curl = curl_init($url);
//        curl_setopt_array(
//            $curl,
//            array(
//                CURLOPT_HTTPHEADER => ['Content-Type:application/json; charset=utf8'],
//                CURLOPT_RETURNTRANSFER => true,
//                CURLOPT_HEADER => false,
//                CURLOPT_USERPWD => $consumer_key . ':' . $consumer_secret
//            )
//        );
//
//        $response = curl_exec($curl);
//        curl_close($curl);
//
//        return $response;


        $response = Http::withBasicAuth($consumer_key, $consumer_secret)
            ->get($url);

        if ($response->failed()) {
            throw new \Exception('Failed to get access token: ' . $response->body());
        }

        $responseData = $response->json();
        
        if (!isset($responseData['access_token'])) {
            throw new \Exception('Access token not found in response: ' . json_encode($responseData));
        }

        return $responseData['access_token'];


    }

    //method to register the urls
    public static function registerURLS()
    {
        $access_token = self::generateAccessToken();
        //get url based on mpesa environment
        $url = config('mpesa.env') == 'sandbox' ?
            'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl' :
            'https://api.safaricom.co.ke/mpesa/c2b/v2/registerurl';
        $confirmation_url = config('mpesa.confirmation_url');
        $validation_url = config('mpesa.validation_url');
        $business_shortcode = config('mpesa.business_shortcode');
        $response_type = 'Completed';

        $response = Http::withToken($access_token)->post($url, [
            'ShortCode' => $business_shortcode,
            'ResponseType' => $response_type,
            'ConfirmationURL' => $confirmation_url,
            'ValidationURL' => $validation_url
        ]);

        return $response->json();

    }

    //STK Push request
    public static function stkPush($phone, $amount, $reference, $userId = null, $invoiceId = null)
    {
        $access_token = self::generateAccessToken();
        //get url based on mpesa environment
        $url = config('mpesa.env') == 'sandbox' ?
            'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest' :
            'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $business_shortcode = config('mpesa.business_shortcode');
        $paybill = config('mpesa.paybill');
        $passkey = config('mpesa.passkey');
        $timestamp = Carbon::now()->format('YmdHis');
        $password = base64_encode($business_shortcode . $passkey . $timestamp);
        $account_reference = $reference;
        $transaction_desc = 'Payment for something';
        $callback_url = config('mpesa.stk_callback_url');

        // Log the request details for debugging
        \Log::info('STK Push Request Details:', [
            'url' => $url,
            'business_shortcode' => $business_shortcode,
            'paybill' => $paybill,
            'phone' => $phone,
            'amount' => $amount,
            'reference' => $reference,
            'timestamp' => $timestamp,
            'environment' => config('mpesa.env')
        ]);

        try {
            $response = Http::withToken($access_token)->post($url, [
                'BusinessShortCode' => $business_shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => $amount,
                'PartyA' => $phone,
                'PartyB' => $business_shortcode,
                'PhoneNumber' => $phone,
                'CallBackURL' => $callback_url,
                'AccountReference' => $reference,
                'TransactionDesc' => 'Rent Payment'
            ]);
        } catch (\Throwable $e) {
            return [
                'status' => 'failed',
                'errorMessage' => $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }

        // Log the response for debugging
        \Log::info('STK Push Response:', [
            'status_code' => $response->status(),
            'response_body' => $response->json(),
            'response_text' => $response->body()
        ]);

        //if respnse has an error, return the error
        if ($response->failed()) {
            $responseBody = $response->json();
            $errorMessage = 'Unknown error';
            
            if ($response->status() === 403) {
                $errorMessage = 'Authentication failed - check consumer key/secret and passkey';
            } elseif ($response->status() === 500) {
                $errorMessage = $responseBody['errorMessage'] ?? 'Server error';
            } elseif (isset($responseBody['errorMessage'])) {
                $errorMessage = $responseBody['errorMessage'];
            }
            
            return [
                'status' => 'failed',
                'errorMessage' => $errorMessage,
                'error' => $responseBody,
                'status_code' => $response->status()
            ];

        }

//        $res = json_decode($response,true);

        $data = $response->json();

        $responseCode = $data['ResponseCode'] ?? null;
        
        //if response code is 0, then the request was successful
        if ($responseCode == 0) {
            $checkout_request_id = $data['CheckoutRequestID'];
            $merchant_request_id = $data['MerchantRequestID'];
            $customer_message = $data['CustomerMessage'];

            //Save to database
            $payment = StkRequest::create([
                'user_id' => $userId,
                'invoice_id' => $invoiceId,
                'phone' => $phone,
                'amount' => $amount,
                'reference' => $reference,
                'CheckoutRequestID' => $checkout_request_id,
                'MerchantRequestID' => $merchant_request_id,
                'description' => $transaction_desc,
                'status' => 'Request Sent'
            ]);

            //return status with checkout request id
            return [
                'status' => 'success',
                'checkout_request_id' => $checkout_request_id,
                'customer_message' => $customer_message
            ];
        }
        
        // Return error response for non-zero response codes
        return [
            'status' => 'failed',
            'errorMessage' => $data['CustomerMessage'] ?? 'Payment request failed',
            'error' => $data
        ];
    }

    //boolean that checks if $request->ip() is in the list of allowed ips from mpesa config
    public static function ipIsFromSafaricom($request): bool
    {
        $allowedIps = config('mpesa.whitelisted_ips');
        if (in_array($request->ip(), $allowedIps)) {
            return true;
        }
        return false;
    }


}
