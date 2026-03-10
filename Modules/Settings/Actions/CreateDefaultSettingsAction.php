<?php

declare(strict_types=1);

namespace Modules\Settings\Actions;

use Modules\Settings\Models\Setting;
use Modules\Settings\Repository\SettingsRepository;

/**
 * Action to create default settings if none exist.
 * This ensures the application always has settings configured.
 */
readonly class CreateDefaultSettingsAction
{
    public function __construct(
        private SettingsRepository $repository
    ) {}

    /**
     * Create default settings with sensible defaults.
     */
    public function execute(): Setting
    {
        $defaults = [
            'description' => 'Modern e-commerce platform with quality products and fast delivery.',
            'short_des' => 'Quality products, fast delivery, best prices',
            'email' => 'info@example.com',
            'phone' => '+1 (555) 123-4567',
            'address' => '123 Main Street, City, Country',
            'site-name' => 'E-Shop',
            'logo' => 'default-logo.png',
            'active_template' => 'default',
            // Payment settings
            'stripe_status' => 'inactive',
            'stripe_key' => null,
            'stripe_secret' => null,
            'paypal_status' => 'inactive',
            'paypal_client_id' => null,
            'paypal_secret' => null,
            // Email settings
            'mail_driver' => 'smtp',
            'mail_host' => 'smtp.mailtrap.io',
            'mail_port' => '2525',
            'mail_username' => null,
            'mail_password' => null,
            'mail_encryption' => 'tls',
            'mail_from_address' => 'noreply@example.com',
            'mail_from_name' => 'E-Shop',
            // Shipping settings
            'free_shipping_threshold' => 100,
            'flat_rate_shipping' => 10,
            // SEO defaults
            'meta_title' => 'E-Shop - Quality Products',
            'meta_description' => 'Your one-stop shop for quality products',
            'meta_keywords' => 'shop, ecommerce, products',
            // Social links
            'facebook_url' => '#',
            'twitter_url' => '#',
            'instagram_url' => '#',
            'youtube_url' => '#',
        ];

        return $this->repository->create($defaults);
    }
}
