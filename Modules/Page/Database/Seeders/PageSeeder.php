<?php

namespace Modules\Page\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Page\Models\Page;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => 'This Privacy Policy explains how we handle your personal information. We respect your privacy and are committed to protecting it through our compliance with this policy.',
                'is_active' => true,
                'user_id' => 3,
            ],
            [
                'title' => 'Shipping Policy',
                'slug' => 'shipping-policy',
                'content' => 'Our Shipping Policy outlines the terms and conditions for shipping our products. We strive to process and ship your order promptly and efficiently.',
                'is_active' => true,
                'user_id' => 3,
            ],
            [
                'title' => 'Payment Policy',
                'slug' => 'payment-policy',
                'content' => 'The Payment Policy details the methods of payment we accept and the procedures for processing transactions. We aim to provide a secure and convenient payment experience.',
                'is_active' => true,
                'user_id' => 3,
            ],
            [
                'title' => 'Terms & Conditions',
                'slug' => 'terms-conditions',
                'content' => 'These Terms & Conditions govern your use of our website and services. By accessing or using our site, you agree to be bound by these terms.',
                'is_active' => true,
                'user_id' => 3,
            ],
            [
                'title' => 'Refund Policy',
                'slug' => 'refund-policy',
                'content' => 'Our Refund Policy provides information on how we handle returns and refunds. We are committed to ensuring your satisfaction with our products.',
                'is_active' => true,
                'user_id' => 3,
            ],
        ];

        foreach ($pages as $page) {
            Page::create($page);
        }
    }
}
