<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    |
    | Gateway default yang digunakan untuk transaksi pembayaran.
    | Supported: 'midtrans', 'xendit', 'doku'
    |
    */

    'default' => env('PAYMENT_GATEWAY', 'midtrans'),

    /*
    |--------------------------------------------------------------------------
    | Payment Expiry (minutes)
    |--------------------------------------------------------------------------
    |
    | Durasi QRIS dalam menit sebelum kedaluwarsa.
    |
    */

    'qr_expiry_minutes' => (int) env('PAYMENT_QR_EXPIRY_MINUTES', 30),

    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    */

    'midtrans' => [
        'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
        'server_key' => env('MIDTRANS_SERVER_KEY', ''),
        'merchant_id' => env('MIDTRANS_MERCHANT_ID', ''),

        // Sandbox URLs
        'snap_url' => env('MIDTRANS_SNAP_URL', 'https://app.sandbox.midtrans.com/snap/v1/transactions'),
        'api_url' => env('MIDTRANS_API_URL', 'https://api.sandbox.midtrans.com'),

        // Production URLs (uncomment for production)
        // 'snap_url' => env('MIDTRANS_SNAP_URL', 'https://app.midtrans.com/snap/v1/transactions'),
        // 'api_url' => env('MIDTRANS_API_URL', 'https://api.midtrans.com'),

        'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

        // JavaScript SDK must use the same environment as the API credentials.
        'snap_js_url' => env(
            'MIDTRANS_SNAP_JS_URL',
            env('MIDTRANS_IS_PRODUCTION', false)
                ? 'https://app.midtrans.com/snap/snap.js'
                : 'https://app.sandbox.midtrans.com/snap/snap.js'
        ),

        'webhook_secret' => env('MIDTRANS_WEBHOOK_SECRET', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Xendit Configuration
    |--------------------------------------------------------------------------
    */

    'xendit' => [
        'secret_key' => env('XENDIT_SECRET_KEY', ''),
        'public_key' => env('XENDIT_PUBLIC_KEY', ''),
        'callback_token' => env('XENDIT_CALLBACK_TOKEN', ''),

        // Sandbox URL
        'api_url' => env('XENDIT_API_URL', 'https://api.xendit.co'),

        // QRIS specific
        'qr_code_method' => 'API',
    ],

    /*
    |--------------------------------------------------------------------------
    | DOKU Configuration
    |--------------------------------------------------------------------------
    */

    'doku' => [
        'client_id' => env('DOKU_CLIENT_ID', ''),
        'secret_key' => env('DOKU_SECRET_KEY', ''),
        'private_key' => env('DOKU_PRIVATE_KEY', ''),

        // Sandbox URL
        'api_url' => env('DOKU_API_URL', 'https://sandbox.doku.com'),
        'api_url_core' => env('DOKU_API_URL_CORE', 'https://sandbox.doku.com/joko/v1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Disbursement / Payout Configuration
    |--------------------------------------------------------------------------
    |
    | Gateway untuk pencairan dana (disbursement/payout).
    | Bisa menggunakan gateway yang sama atau berbeda.
    |
    */

    'disbursement' => [
        'enabled' => env('DISBURSEMENT_ENABLED', false),

        // Gateway yang digunakan untuk disbursement
        // Supported: 'xendit', 'midtrans', 'doku'
        'gateway' => env('DISBURSEMENT_GATEWAY', 'xendit'),

        'xendit' => [
            'secret_key' => env('XENDIT_SECRET_KEY', ''),
            'callback_token' => env('XENDIT_DISBURSEMENT_CALLBACK_TOKEN', ''),
            'api_url' => env('XENDIT_API_URL', 'https://api.xendit.co'),
        ],

        'midtrans' => [
            'server_key' => env('MIDTRANS_SERVER_KEY', ''),
            'api_url' => env('MIDTRANS_API_URL', 'https://api.sandbox.midtrans.com'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    */

    'webhook' => [
        // Path untuk webhook endpoint
        'path' => env('PAYMENT_WEBHOOK_PATH', 'payments/webhook'),

        // IP whitelist untuk webhook (kosongkan untuk allow all)
        'ip_whitelist' => array_filter(explode(',', env('PAYMENT_WEBHOOK_IP_WHITELIST', ''))),
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    */

    'currency' => 'IDR',

];
