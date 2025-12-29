<?php

declare(strict_types=1);

namespace Modules\Settings\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Settings\Models\Setting;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        $appName = config('app.name', 'E-commerce Store');
        $appUrl = config('app.url', 'http://localhost');

        return [
            'description' => 'Modern e-commerce platform with quality products and fast delivery.',
            'short_des' => 'Quality products, fast delivery, best prices',
            'logo' => '/assets/img/logo/logo.png',
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'site-name' => $appName,
            'active_template' => 'default',
            'keywords' => 'online shopping, ecommerce, products, deals, discounts',
            'google-site-verification' => '',
            'longitude' => null,
            'latitude' => null,
            'google_map_api_key' => null,
            'seo_settings' => [
                'meta_title' => $appName.' - Quality Products Online',
                'meta_description' => 'Shop online for quality products with fast delivery and secure payment. Best deals and discounts available.',
                'meta_keywords' => 'online shopping, ecommerce, products, deals, discounts, quality products, fast delivery',
                'og_title' => $appName.' - Quality Products Online',
                'og_description' => 'Shop online for quality products with fast delivery and secure payment. Best deals and discounts available.',
                'og_image' => $appUrl.'/assets/img/logo/logo.png',
                'og_type' => 'website',
                'og_site_name' => $appName,
                'twitter_card' => 'summary_large_image',
                'twitter_site' => '@ecommercestore',
                'twitter_title' => $appName.' - Quality Products Online',
                'twitter_description' => 'Shop online for quality products with fast delivery and secure payment.',
                'twitter_image' => $appUrl.'/assets/img/logo/logo.png',
                'google_analytics_id' => null,
                'google_tag_manager_id' => null,
                'facebook_pixel_id' => null,
            ],
            'payment_settings' => [
                'stripe_enabled' => false,
                'stripe_public_key' => null,
                'stripe_secret_key' => null,
                'paypal_enabled' => false,
                'paypal_client_id' => null,
                'paypal_client_secret' => null,
                'paypal_mode' => 'sandbox',
                'cod_enabled' => true,
                'bank_transfer_enabled' => false,
                'bank_account_details' => null,
            ],
            'shipping_settings' => [
                'default_shipping_method' => 'standard',
                'free_shipping_threshold' => 100,
                'standard_shipping_cost' => 10,
                'express_shipping_cost' => 20,
            ],
            'email_settings' => [
                'mail_driver' => 'log',
                'mail_host' => env('MAIL_HOST', 'smtp.mailtrap.io'),
                'mail_port' => env('MAIL_PORT', '2525'),
                'mail_username' => env('MAIL_USERNAME'),
                'mail_password' => env('MAIL_PASSWORD'),
                'mail_encryption' => env('MAIL_ENCRYPTION', 'tls'),
                'mail_from_address' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
                'mail_from_name' => env('MAIL_FROM_NAME', $appName),
            ],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
