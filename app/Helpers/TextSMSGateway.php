<?php

namespace App\Helpers;

use Http;

class TextSMSGateway
{

    public static function sendSms(string $phone, string $message)
    {
        //POST to  https://sms.textsms.co.ke/api/services/sendsms/
        //REquest body
        //        {
        //            "apikey":"123456789",
        // "partnerID":"123",
        // "message":"this is a test message",
        // "shortcode":"SENDERID",
        // "mobile":"254712345678"
        //}

        //Success {
        // "responses": [
        //	{
        // "respose-code": 200,
        // "response-description": "Success",
        // "mobile": 254712345678,
        // "messageid": 8290842,
        // "networkid": "1"
        //	}
        //		]
        // }

        //Show implementation of this method

        $baseUrl = 'https://sms.textsms.co.ke/api/services/sendsms/';
        $apiKey = 'e157fde83608790bc6cbbabd6aaf58b0';
        $partnerId = '8048';
        $shortcode = 'PV_Tech';


        try {
            $response = Http::post($baseUrl, [
                'apikey' => $apiKey,
                'partnerID' => $partnerId,
                'message' => $message,
                'shortcode' => $shortcode,
                'mobile' => $phone,
            ]);

            return $response->json();
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }


    }

}
