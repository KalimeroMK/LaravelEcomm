<?php

/**
 * Stripe Setting & API Credentials
 * Created by Zoran Shefot Bogoevski.
 */

return [
    'mode'    => env('STRIPE_MODE', 'sandbox'),
    // Can only be 'sandbox' Or 'live'. If empty or invalid, 'live' will be used.
    'sandbox' => [
        'client_key'    => env('STRIPE_SANDBOX_PUBLISH_KEY', ''),
        'client_secret' => env('STRIPE_SANDBOX_SECRET', ''),
    ],
    'live'    => [
        'client_key'    => env('STRIPE_SANDBOX_PUBLISH_KEY', ''),
        'client_secret' => env('STRIPE_SANDBOX_SECRET', ''),
    ],
];
