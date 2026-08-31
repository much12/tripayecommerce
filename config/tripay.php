<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mode TriPay
    |--------------------------------------------------------------------------
    | "sandbox" untuk pengembangan, "production" untuk transaksi asli.
    */
    'mode' => env('TRIPAY_MODE', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Kredensial Merchant
    |--------------------------------------------------------------------------
    | Didapatkan dari dashboard merchant TriPay.
    */
    'api_key' => env('TRIPAY_API_KEY'),
    'private_key' => env('TRIPAY_PRIVATE_KEY'),
    'merchant_code' => env('TRIPAY_MERCHANT_CODE'),

    /*
    |--------------------------------------------------------------------------
    | Base URL API
    |--------------------------------------------------------------------------
    */
    'base_url' => [
        'sandbox' => 'https://tripay.co.id/api-sandbox',
        'production' => 'https://tripay.co.id/api',
    ],

];
