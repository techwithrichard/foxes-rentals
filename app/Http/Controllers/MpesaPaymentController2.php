<?php

namespace App\Http\Controllers;

use App\Models\C2bRequest;
use App\Models\StkRequest;
use App\Services\MPesaHelper;
use Illuminate\Http\Request;
use Log;

class MpesaPaymentController extends Controller

{
    public function confirmation(Request $request)
    {

        if (!MPesaHelper::ipIsFromSafaricom($request)) {
            return response('Request is not from Safaricom', 403);
        }

        // Log::info('STK Callback Response: ' .file_get_contents('php://input'));
        Log::info('STK Callback Response: ' .$request->getContent());

        $data = $request->getContent();
        Storage::disk('local')->put('confirmation.txt', $data);

        $response = json_decode($data);
        if(!$response){
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Invalid JSON data'], 400);
        }
        // extract the values
        try {
            C2bRequest::create([
                "TransactionType" => $response->TransactionType,
                "TransID" => $response->TransID,
                "TransTime" => $response->TransTime,
                "TransAmount" => $response->TransAmount,
                "BusinessShortCode" => $response->BusinessShortCode,
                "BillRefNumber" => $response->BillRefNumber,
                "InvoiceNumber" => $response->InvoiceNumber,
                "OrgAccountBalance" => $response->OrgAccountBalance,
                "ThirdPartyTransID" => $response->ThirdPartyTransID,
                "MSISDN" => $response->MSISDN,
                "FirstName" => $response->FirstName,
            ]);

        }catch(\Exception $e){
            Log::error('Error saving confirmation data: '. $e->getMessage());
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Error saving confirmation data'], 500);
        }
      

        return response()->json(
            [
                'ResultCode' => 0,
                'ResultDesc' => 'Accepted'
            ]
        );

//        return 'Confirmation URL Called';

    }

    public function validation()
    {
       $data = request()->getContent();
       Storage::disk('local')->put('validation.txt', $data);

        return response()->json(
            [
                'ResultCode' => 0,
                'ResultDesc' => 'Accepted'
            ]
        );


//        return 'Validation URL Called';

    }

    public function stk(Request $request)
    {

        if (!MPesaHelper::ipIsFromSafaricom($request)) {
            return response('Request is not from Safaricom', 403);
        }

        //Log the response
        Log::info('STK Callback Response: ' . $request->getContent());

        $data = $request->getContent();
        Storage::disk('local')->put('stk.txt', $data);

        $response_obj = json_decode($data);

        // extract the values
        $merchant_request_id = $response_obj->Body->stkCallback->MerchantRequestID;
        $checkout_request_id = $response_obj->Body->stkCallback->CheckoutRequestID;
        $result_code = $response_obj->Body->stkCallback->ResultCode;
        $result_desc = $response_obj->Body->stkCallback->ResultDesc;
        if ($result_code == 0) {


            $callbackMetadata = $response_obj->Body->stkCallback->CallbackMetadata->Item;
            $amount = null;
            $mpesaReceiptNumber = null;
            $phoneNumber = null;
            foreach ($callbackMetadata as $item) {
                switch ($item->Name) {
                    case 'Amount':
                        $amount = $item->Value ?? '';
                        break;
                    case 'MpesaReceiptNumber':
                        $mpesaReceiptNumber = $item->Value ?? '';
                        break;
                    case 'PhoneNumber':
                        $phoneNumber = $item->Value ?? '';
                        break;
                    default:
                        // Handle any other values here, if needed
                        break;
                }
            }

            $stkPayment = StkRequest::where('MerchantRequestID', $merchant_request_id)->firstOrFail();
            $stkPayment->status = 'Paid';
            $stkPayment->MpesaReceiptNumber = $mpesaReceiptNumber;


            // You can also send an SMS or email notification to the user here,
            // informing them that their payment was successful.


        } else {
            $stkPayment = StkRequest::where('MerchantRequestID', $merchant_request_id)->firstOrFail();
            $stkPayment->status = 'Failed';
        }
        $stkPayment->ResultDesc = $result_desc;
        $stkPayment->save();


    }
    
}
