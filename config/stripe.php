<?php

declare(strict_types=1);

return [
    'mode' => env('STRIPE_MODE', 'sandbox'),
    
    'sandbox' => [
        'client_key' => env('STRIPE_KEY', ''),
        'client_secret' => env('STRIPE_SECRET', ''),
    ],
    
    'live' => [
        'client_key' => env('STRIPE_LIVE_KEY', ''),
        'client_secret' => env('STRIPE_LIVE_SECRET', ''),
    ],
    
    'currency' => env('STRIPE_CURRENCY', 'usd'),
];
