<?php

declare(strict_types=1);

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Settings\Models\Setting;

class SettingsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        // Create or update default settings with proper SEO data
        $setting = Setting::firstOrNew(['id' => 1]);

        $setting->fill([
            'description' => 'Modern e-commerce platform with quality products and fast delivery. Shop online for the best deals and discounts.',
            'short_des' => 'Quality products, fast delivery, best prices',
            'logo' => '/assets/img/logo/logo.png',
            'address' => '123 Main Street, City, Country',
            'phone' => '+1 (555) 123-4567',
            'email' => 'info@example.com',
            'site-name' => config('app.name', 'E-commerce Store'),
            'active_template' => 'default',
            'keywords' => 'online shopping, ecommerce, products, deals, discounts, quality products',
            'google-site-verification' => '',
            'longitude' => null,
            'latitude' => null,
            'google_map_api_key' => null,
        ]);

        // Set proper SEO settings
        $setting->seo_settings = [
            'meta_title' => config('app.name', 'E-commerce Store').' - Quality Products Online',
            'meta_description' => 'Shop online for quality products with fast delivery and secure payment. Best deals and discounts available.',
            'meta_keywords' => 'online shopping, ecommerce, products, deals, discounts, quality products, fast delivery',
            'og_title' => config('app.name', 'E-commerce Store').' - Quality Products Online',
            'og_description' => 'Shop online for quality products with fast delivery and secure payment. Best deals and discounts available.',
            'og_image' => config('app.url', 'http://localhost').'/assets/img/logo/logo.png',
            'og_type' => 'website',
            'og_site_name' => config('app.name', 'E-commerce Store'),
            'twitter_card' => 'summary_large_image',
            'twitter_site' => '@ecommercestore',
            'twitter_title' => config('app.name', 'E-commerce Store').' - Quality Products Online',
            'twitter_description' => 'Shop online for quality products with fast delivery and secure payment.',
            'twitter_image' => config('app.url', 'http://localhost').'/assets/img/logo/logo.png',
            'google_analytics_id' => null,
            'google_tag_manager_id' => null,
            'facebook_pixel_id' => null,
        ];

        // Set default payment settings
        $setting->payment_settings = [
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
        ];

        // Set default shipping settings
        $setting->shipping_settings = [
            'default_shipping_method' => 'standard',
            'free_shipping_threshold' => 100,
            'standard_shipping_cost' => 10,
            'express_shipping_cost' => 20,
        ];

        // Set default email settings
        $setting->email_settings = [
            'mail_driver' => 'log',
            'mail_host' => env('MAIL_HOST', 'smtp.mailtrap.io'),
            'mail_port' => env('MAIL_PORT', '2525'),
            'mail_username' => env('MAIL_USERNAME'),
            'mail_password' => env('MAIL_PASSWORD'),
            'mail_encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'mail_from_address' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
            'mail_from_name' => env('MAIL_FROM_NAME', config('app.name', 'E-commerce Store')),
        ];

        $setting->save();

        Model::reguard();
    }
}
