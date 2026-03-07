<?php

declare(strict_types=1);

return [
    'mode' => env('PAYPAL_MODE', 'sandbox'),
    
    'sandbox' => [
        'client_id' => env('PAYPAL_SANDBOX_CLIENT_ID', ''),
        'client_secret' => env('PAYPAL_SANDBOX_CLIENT_SECRET', ''),
    ],
    
    'live' => [
        'client_id' => env('PAYPAL_LIVE_CLIENT_ID', ''),
        'client_secret' => env('PAYPAL_LIVE_CLIENT_SECRET', ''),
    ],
    
    'currency' => env('PAYPAL_CURRENCY', 'USD'),
    
    'notify_url' => env('PAYPAL_NOTIFY_URL', ''),
];
