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


        return $response->json()['access_token'];


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
    public static function stkPush($phone, $amount, $reference)
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

        try {
            $response = Http::withToken($access_token)->post($url, [
                'BusinessShortCode' => $business_shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => $amount,
                'PartyA' => $phone,
                'PartyB' => $paybill,
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

        //if respnse has an error, return the error
        if ($response->failed()) {
            return [
                'status' => 'failed',
                'errorMessage' => $response->json()['errorMessage'] ?? $response->json(),
                'error' => $response->json()
            ];

        }

//        $res = json_decode($response,true);

        $data = json_decode($response, true);

        $responseCode = $data['ResponseCode'];
        //if response code is 0, then the request was successful
        if ($responseCode == 0) {
            $checkout_request_id = $data['CheckoutRequestID'];
            $merchant_request_id = $data['MerchantRequestID'];
            $customer_message = $data['CustomerMessage'];

            //Save to database

            $payment = StkRequest::create([
                    'phone' => $phone,
                    'amount' => $amount,
                    'reference' => $reference,
                    'CheckoutRequestID' => $checkout_request_id,
                    'MerchantRequestID' => $merchant_request_id,
                    'description' => $transaction_desc,
                    'status' => 'Request Sent'
                ]
            );

            //return the checkout request id
//            return $customer_message;

            //return status with checkout request id
            return [
                'status' => 'success',
                'checkout_request_id' => $checkout_request_id
            ];


        }
        return null;
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
