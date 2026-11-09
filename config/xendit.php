<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Xendit Payment Gateway
    |--------------------------------------------------------------------------
    |
    | Konfigurasi kredensial dan endpoint Xendit (Sandbox / Production).
    | Secret KEY disimpan hanya di server (.env), tidak pernah dikirim ke frontend.
    | Frontend hanya menerima invoice_url dari backend untuk diarahkan ke Xendit.
    */

    'secret_key' => env('XENDIT_SECRET_KEY'),

    'callback_token' => env('XENDIT_CALLBACK_TOKEN'),

    'base_url' => env('XENDIT_BASE_URL', 'https://api.xendit.co'),

    'api_version' => 'v2',

    'callback_url' => env('XENDIT_CALLBACK_URL'),

    'redirect_url' => env('XENDIT_REDIRECT_URL'),

];
