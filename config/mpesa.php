<?php

return [

    'consumer_key' => env('MPESA_CONSUMER_KEY', 'YOUR_CONSUMER_KEY'),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET', 'YOUR_CONSUMER_SECRET'),
    'business_shortcode' => env('MPESA_SHORTCODE', 174379),
    'paybill' => env('MPESA_PAYBILL', 174379),
    'passkey' => env('MPESA_PASSKEY', 'YOUR_PASSKEY'),
    'confirmation_url' => env('MPESA_CONFIRMATION_URL', 'YOUR_CONFIRMATION_URL'),
    'validation_url' => env('MPESA_VALIDATION_URL', 'YOUR_VALIDATION_URL'),
    'stk_callback_url' => env('MPESA_STK_CALLBACK_URL', 'YOUR_STK_CALLBACK_URL'),
    'unique_mpesa_token' => env('MPESA_UNIQUE_TOKEN', '8jKU1x6dHrYpAQ7L'),
    'env' => env('MPESA_ENV', 'sandbox'), //live or sandbox
    'whitelisted_ips' => [
        '196.201.214.200',
        '196.201.214.206',
        '196.201.213.114',
        '196.201.214.207',
        '196.201.214.208',
        '196.201.213.44',
        '196.201.212.127',
        '196.201.212.138',
        '196.201.212.129',
        '196.201.212.136',
        '196.201.212.74',
        '196.201.212.69'

    ]


];
