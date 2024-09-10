<?php

namespace App\Http\Controllers\Tenant;

use App\Events\InvoicePaidEvent;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Omnipay\Omnipay;

class PaypalPaymentController extends Controller
{
    private $gateway;

    public function __construct()
    {
        $this->gateway = Omnipay::create('PayPal_Rest');
        $this->gateway->setClientId(env('PAYPAL_CLIENT_ID'));
        $this->gateway->setSecret(env('PAYPAL_SECRET'));
        $this->gateway->setTestMode(env('PAYPAL_MODE'));
    }

    public function pay(Request $request, $invoiceId)
    {
        try {
            $response = $this->gateway->purchase([
                'amount' => '10.00',
                'currency' => 'USD',
                'returnUrl' => route('tenant.paypal_payment_success', $invoiceId),
                'cancelUrl' => route('tenant.paypal_payment_cancel'),
            ])->send();

            if ($response->isRedirect()) {
                $response->redirect();
            } else {
                // not successful
                $error_message = $response->getMessage();
                return view('tenant.invoices.cancel_payment', compact('error_message'));
            }


        } catch (\Throwable $th) {
            //throw $th;

            $error_message = $th->getMessage();
            return view('tenant.invoices.cancel_payment', compact('error_message'));
        }

        return null;


    }

    public function success(Request $request, $invoiceId)
    {
        if ($request->input('paymentId') && $request->input('PayerID')) {
            $transaction = $this->gateway->completePurchase([
                'payer_id' => $request->input('PayerID'),
                'transactionReference' => $request->input('paymentId'),
            ])->send();

            if ($transaction->isSuccessful()) {
                $data = $transaction->getData();
                $amount_paid = $data['transactions'][0]['amount']['total'];
                $transaction_id = $data['id'];


                $this->completePayment($invoiceId,
                    $transaction_id,
                    $amount_paid);

                return view('tenant.invoices.payment_success');
            } else {

                $error_message = $transaction->getMessage();
                return view('tenant.invoices.cancel_payment', compact('error_message'));
            }
        } else {
            $error_message = 'Payment failed.Try again later';
            return view('tenant.invoices.cancel_payment', compact('error_message'));
        }


    }


    public function returnUrl()
    {
        return view('tenant.invoices.payment_success');
    }

    public function cancelUrl()
    {
        return view('tenant.invoices.cancel_payment');
    }

    protected function completePayment($invoiceId, $transactionId, $amount)
    {
        //get invoice
        $invoice = Invoice::find($invoiceId);

        DB::transaction(function () use ($amount, $invoice, $transactionId) {
            $invoice->pay($amount);
            Payment::create([
                'amount' => $amount,
                'paid_at' => now(),
                'payment_method' => 'PAYPAL',
                'reference_number' => $transactionId,
                'tenant_id' => $invoice->tenant_id,
                'invoice_id' => $invoice->id,
                'landlord_id' => $invoice->landlord_id,
                'commission' => $invoice->commission,
                'property_id' => $invoice->property_id,
                'house_id' => $invoice->house_id,


            ]);
            InvoicePaidEvent::dispatch($invoice);
        });
    }
}
