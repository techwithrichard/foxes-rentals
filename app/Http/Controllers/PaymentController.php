<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    //generate token
    public function token(){
        $consumerKey = 'c779zA18GytQZv2wwrG79LiZLMMt3oi6DvbtiQGNlxb26Wau';
        $consumerSecret = 'HmPmTuCE9WB5NZ8hH124H9WW1HC9cIJSZaSRzRoHVpE2G7Guo5ktU9hWW9uQNZf0';
        $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $response =Http::withBasicAuth($consumerKey, $consumerSecret)->get($url);
        return $response['access_token'];
    }

    // stk push
    public function initiateStkPush(){
        $accessToken=$this->token();
        $url ='https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $passKey='bfb279f9aa9bdbcf158e97dd71a467c d2e0c893059b10f78e6b72ada1ed2c919'; 
        $BusinessShortCode=174379;
        $Timestamp=Carbon::now()->format('YmdHis');
        $password=base64_encode($BusinessShortCode.$passKey.$Timestamp);
        $TransactionType='CustomerPayBillOnline';
        $Amount=1;
        $PartyA=254720691181;
        $PartyB=174379;
        $PhoneNumber=254720691181;
        $CallBackURL='https://25db-41-80-113-54.ngrok-free.app/payments/stkcallback';
        $AccountReference='Foxes Rental Systems';
        $TransactionDesc='Payment for rent';

        $response =Http::withToken($accessToken)->post($url,
        [       
            'TransactionType' => $TransactionType,
            'Amount' => $Amount,
            'PartyA' => $PartyA,
            'PartyB' => $PartyB,
            'PhoneNumber' => $PhoneNumber,
            'CallBackURL' => $CallBackURL,
            'AccountReference' => $AccountReference,
            'TransactionDesc' => $TransactionDesc,
            'Password' => $password,
            'Timestamp' => $Timestamp,
            'BusinessShortCode' => $BusinessShortCode,
            'ResponseType' => 'Completed',
            'TransactionID' => Carbon::now()->format('YmdHis').rand(1000,9999)
            ]
    );

    return $response;



    }

    // stk call 
    public function stkCallback(){
        $response = file_get_contents('php://input');
        Storage::disk('local')->put('stk_callback.txt', $response);
        // return 'Callback Received';
    }
}
