<?php

declare(strict_types=1);

return [
    'name' => 'Language',
    
    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | This value is the default locale that will be used by the localization
    | features. You are free to set this value to any of the locales which
    | will be supported by the application.
    |
    */
    'locale' => env('APP_LOCALE', 'en'),
    
    /*
    |--------------------------------------------------------------------------
    | Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    
    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),
    
    /*
    |--------------------------------------------------------------------------
    | Available Locales
    |--------------------------------------------------------------------------
    |
    | These are the locales that are available in the application.
    | They are loaded from the database on runtime.
    |
    */
    'available_locales' => ['en', 'mk', 'de', 'sq'],
];
