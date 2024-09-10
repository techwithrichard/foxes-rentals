<?php

namespace App\Helpers;

use Omnipay\Omnipay;

class PaypalGateway
{
    public function gateway()
    {
        $gateway = Omnipay::create('PayPal_Express');

        $gateway->setUsername(team_setting('pp_username'));
        $gateway->setPassword(team_setting('pp_password'));
        $gateway->setSignature(team_setting('pp_signature'));
        $gateway->setTestMode(team_setting('pp_is_sandbox'));

        return $gateway;
    }


    public function purchase(array $parameters)
    {
        return $this->gateway()
            ->purchase($parameters)
            ->send();
    }

    public function complete(array $parameters)
    {
        return $this->gateway()
            ->completePurchase($parameters)
            ->send();
    }

    public function formatAmount($amount)
    {
        return number_format($amount, 2, '.', '');
    }


    public function getCancelUrl(): string
    {
        return route('tenant.paypal_payment_cancel');
    }


    public function getReturnUrl($invoiceId): string
    {
        return route('tenant.paypal_payment_success', $invoiceId);
    }

    /**
     * @param $order
     */
    public function getNotifyUrl($order)
    {
        $env = config('services.paypal.sandbox') ? "sandbox" : "live";

        return route('webhook.paypal.ipn', [1, $env]);
    }

}
