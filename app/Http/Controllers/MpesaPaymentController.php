<?php

namespace App\Http\Controllers;

use App\Models\C2bRequest;
use App\Models\StkRequest;
use App\Notifications\PaymentProofStatusNotification;
use App\Services\MPesaHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MpesaPaymentController extends Controller
{
    public function confirmation(Request $request)
    {
        if (!MPesaHelper::ipIsFromSafaricom($request)) {
            return response('Request is not from Safaricom', 403);
        }

        Log::info('C2B Confirmation Request: ' . $request->getContent());

        $data = $request->getContent();
        Storage::disk('local')->put('confirmation.txt', $data);

        $response = json_decode($data);
        if (!$response) {
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Invalid JSON data'], 400);
        }

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

            // You can add notification logic here if needed

        } catch (\Exception $e) {
            Log::error('Error saving confirmation data: ' . $e->getMessage());
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Error saving confirmation data'], 500);
        }

        return response()->json(
            [
                'ResultCode' => 0,
                'ResultDesc' => 'Accepted'
            ]
        );
    }

    public function validation(Request $request)
    {
        $data = $request->getContent();
        Storage::disk('local')->put('validation.txt', $data);

        // Perform any validation logic if needed

        return response()->json(
            [
                'ResultCode' => 0,
                'ResultDesc' => 'Accepted'
            ]
        );
    }

    public function stk(Request $request)
    {
        if (!MPesaHelper::ipIsFromSafaricom($request)) {
            return response('Request is not from Safaricom', 403);
        }

        Log::info('STK Callback Response: ' . $request->getContent());

        $data = $request->getContent();
        Storage::disk('local')->put('stk.txt', $data);

        $response_obj = json_decode($data);

        if (isset($response_obj->Body->stkCallback)) {
            $stkCallback = $response_obj->Body->stkCallback;
            $merchant_request_id = $stkCallback->MerchantRequestID ?? null;
            $checkout_request_id = $stkCallback->CheckoutRequestID ?? null;
            $result_code = $stkCallback->ResultCode ?? null;
            $result_desc = $stkCallback->ResultDesc ?? '';

            try {
                $stkPayment = StkRequest::where('MerchantRequestID', $merchant_request_id)->firstOrFail();

                if ($result_code === 0) {
                    $callbackMetadata = $stkCallback->CallbackMetadata->Item ?? [];
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
                        }
                    }

                    $stkPayment->status = 'Paid';
                    $stkPayment->MpesaReceiptNumber = $mpesaReceiptNumber;
                    $stkPayment->amount = $amount; // Ensure to save the amount

                    // Retrieve the user associated with the payment
                    $user = $stkPayment->user; // Assuming there's a relationship set up between StkRequest and User

                    // Prepare notification data
                    $notificationData = [
                        'amount' => $amount,
                        'reference_number' => $mpesaReceiptNumber,
                        'status' => 'approved',
                        'remarks' => 'Your payment has been successfully processed.'
                    ];

                    // Send email notification
                    if ($user) {
                        $user->notify(new PaymentProofStatusNotification($notificationData));
                    }

                } else {
                    $stkPayment->status = 'Failed';

                    // Prepare notification data for failed payment
                    $notificationData = [
                        'amount' => 'N/A',
                        'reference_number' => 'N/A',
                        'status' => 'rejected',
                        'remarks' => $result_desc
                    ];

                    // Retrieve the user associated with the payment
                    $user = $stkPayment->user;

                    // Send email notification for failed payment
                    if ($user) {
                        $user->notify(new PaymentProofStatusNotification($notificationData));
                    }
                }

                $stkPayment->ResultDesc = $result_desc;
                $stkPayment->save();

            } catch (\Exception $e) {
                Log::error('Error processing STK callback: ' . $e->getMessage());
                return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Error processing STK callback'], 500);
            }

            return response('Callback processed', 200);
        } else {
            Log::error('Invalid STK Callback Response: ' . $data);
            return response('Invalid callback data', 400);
        }
    }
}
