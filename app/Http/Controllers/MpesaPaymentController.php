<?php

namespace App\Http\Controllers;

use App\Models\C2bRequest;
use App\Models\StkRequest;
use App\Notifications\PaymentProofStatusNotification;
use App\Services\MPesaHelper;
use App\Services\PaymentReconciliationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MpesaPaymentController extends Controller
{
    public function confirmation(Request $request)
    {
        Log::info('C2B Confirmation Request received from IP: ' . $request->ip());
        Log::info('C2B Confirmation Request headers: ' . json_encode($request->headers->all()));
        Log::info('C2B Confirmation Request content: ' . $request->getContent());

        if (!MPesaHelper::ipIsFromSafaricom($request)) {
            Log::warning('C2B Confirmation Request blocked - IP not whitelisted: ' . $request->ip());
            return response('Request is not from Safaricom', 403);
        }

        Log::info('C2B Confirmation Request IP validated successfully');

        $data = $request->getContent();
        Storage::disk('local')->put('confirmation.txt', $data);

        $response = json_decode($data);
        if (!$response) {
            Log::error('C2B Confirmation Request - Invalid JSON data: ' . $data);
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Invalid JSON data'], 400);
        }

        Log::info('C2B Confirmation Request JSON parsed successfully: ' . json_encode($response));

        try {
            Log::info('Creating C2B request record for transaction: ' . $response->TransID);
            
            $c2bRequest = C2bRequest::create([
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
            
            Log::info('C2B request record created successfully with ID: ' . $c2bRequest->id);

            // Attempt automatic reconciliation for C2B payments
            $reconciliationService = new PaymentReconciliationService();
            $reconciliationResult = $reconciliationService->processC2bCallback([
                'MSISDN' => $response->MSISDN,
                'TransAmount' => $response->TransAmount,
                'TransID' => $response->TransID,
            ]);

            if ($reconciliationResult['success']) {
                if (isset($reconciliationResult['invoice_completed']) && $reconciliationResult['invoice_completed']) {
                    Log::info('C2B payment split - invoice completed and overpayment recorded: ' . $response->TransID . ' - ' . $reconciliationResult['message']);
                } else {
                    Log::info('C2B payment auto-reconciled: ' . $response->TransID);
                }
            } else {
                Log::info('C2B payment requires manual verification: ' . $response->TransID . ' - ' . $reconciliationResult['message']);
            }

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

                    // Retrieve the user and invoice associated with the payment
                    $user = $stkPayment->user;
                    $invoice = $stkPayment->invoice;

                    // If we have an invoice, automatically reconcile the payment
                    if ($invoice) {
                        try {
                            // Check if this payment would cause an overpayment
                            $reconciliationService = new PaymentReconciliationService();
                            $overpaymentCheck = $reconciliationService->checkForDuplicatePayment($phoneNumber, $amount);
                            
                            if ($overpaymentCheck['is_duplicate']) {
                                Log::info("STK overpayment detected: Phone {$phoneNumber}, Amount {$amount}, Invoice: {$overpaymentCheck['invoice']->invoice_id}, Overpayment: {$overpaymentCheck['overpayment_amount']}");
                                
                                // Handle as overpayment by splitting the payment
                                $overpaymentResult = $reconciliationService->handleDuplicatePayment($phoneNumber, $amount, $mpesaReceiptNumber, $overpaymentCheck);
                                
                                if ($overpaymentResult['success']) {
                                    Log::info('STK payment split - invoice completed and overpayment recorded: ' . $mpesaReceiptNumber);
                                } else {
                                    Log::error('STK overpayment handling failed: ' . $overpaymentResult['message']);
                                }
                            } else {
                                \DB::beginTransaction();
                                
                                // Create payment entry
                                \App\Models\Payment::create([
                                    'amount' => $amount,
                                    'paid_at' => now(),
                                    'payment_method' => 'MPESA STK',
                                    'reference_number' => $mpesaReceiptNumber,
                                    'tenant_id' => $invoice->tenant_id,
                                    'invoice_id' => $invoice->id,
                                    'recorded_by' => $user ? $user->id : null,
                                    'landlord_id' => $invoice->landlord_id,
                                    'commission' => $invoice->commission,
                                    'property_id' => $invoice->property_id,
                                    'house_id' => $invoice->house_id,
                                    'status' => \App\Enums\PaymentStatusEnum::PAID,
                                ]);

                                // Reconcile payment with invoice
                                $invoice->pay($amount);
                                \App\Events\InvoicePaidEvent::dispatch($invoice);
                                
                                \DB::commit();
                                Log::info('STK payment auto-reconciled: ' . $mpesaReceiptNumber);
                            }
                        } catch (\Exception $e) {
                            \DB::rollBack();
                            Log::error('Error reconciling STK payment: ' . $e->getMessage());
                        }
                    }

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
                    // Map result codes to specific statuses
                    $status = $this->mapResultCodeToStatus($result_code, $result_desc);
                    $stkPayment->status = $status;

                    Log::info("STK Payment Failed - Result Code: {$result_code}, Description: {$result_desc}, Status: {$status}");

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

    /**
     * Map M-PESA result codes to specific statuses
     */
    private function mapResultCodeToStatus($resultCode, $resultDesc)
    {
        // M-PESA STK Push Result Codes
        switch ($resultCode) {
            case 1:
                return 'Cancelled by User';
            case 2:
                return 'Insufficient Funds';
            case 3:
                return 'Wrong PIN';
            case 4:
                return 'Timeout';
            case 5:
                return 'Transaction Failed';
            case 6:
                return 'Network Error';
            case 7:
                return 'Service Unavailable';
            case 8:
                return 'Invalid Amount';
            case 9:
                return 'Invalid Account';
            case 10:
                return 'Duplicate Transaction';
            case 11:
                return 'Account Blocked';
            case 12:
                return 'Daily Limit Exceeded';
            case 13:
                return 'Transaction Limit Exceeded';
            case 14:
                return 'Invalid Phone Number';
            case 15:
                return 'Invalid Business Number';
            case 16:
                return 'Invalid Reference';
            case 17:
                return 'System Error';
            case 18:
                return 'Maintenance Mode';
            case 19:
                return 'Invalid Transaction Type';
            case 20:
                return 'Invalid Currency';
            default:
                // Check description for additional context
                $desc = strtolower($resultDesc);
                if (str_contains($desc, 'cancel')) {
                    return 'Cancelled by User';
                } elseif (str_contains($desc, 'insufficient')) {
                    return 'Insufficient Funds';
                } elseif (str_contains($desc, 'pin')) {
                    return 'Wrong PIN';
                } elseif (str_contains($desc, 'timeout')) {
                    return 'Timeout';
                } elseif (str_contains($desc, 'network')) {
                    return 'Network Error';
                } elseif (str_contains($desc, 'service')) {
                    return 'Service Unavailable';
                } else {
                    return 'Failed - ' . $resultDesc;
                }
        }
    }
}
