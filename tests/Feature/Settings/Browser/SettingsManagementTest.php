<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Settings\Models\Setting;

require_once __DIR__.'/../../../TestHelpers.php';

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = createAdminUser();
});

test('admin can view settings page', function () {
    $response = $this->actingAs($this->admin)
        ->get('/admin/settings');

    $response->assertStatus(200);
    $response->assertSee('Settings');
});

test('admin can update general settings', function () {
    $settingsData = [
        'site_name' => 'Test Store',
        'site_description' => 'Test Description',
        'site_email' => 'admin@teststore.com',
        'site_phone' => '+1234567890',
    ];

    $response = $this->actingAs($this->admin)
        ->put('/admin/settings/general', $settingsData);

    $response->assertRedirect();

    foreach ($settingsData as $key => $value) {
        $this->assertDatabaseHas('settings', [
            'key' => $key,
            'value' => $value,
        ]);
    }
});

test('admin can update payment settings', function () {
    $settingsData = [
        'payment_methods' => ['cod', 'paypal', 'stripe'],
        'paypal_client_id' => 'test_paypal_id',
        'stripe_public_key' => 'test_stripe_key',
        'stripe_secret_key' => 'test_stripe_secret',
    ];

    $response = $this->actingAs($this->admin)
        ->put('/admin/settings/payment', $settingsData);

    $response->assertRedirect();

    foreach ($settingsData as $key => $value) {
        $this->assertDatabaseHas('settings', [
            'key' => $key,
            'value' => is_array($value) ? json_encode($value) : $value,
        ]);
    }
});

test('admin can update shipping settings', function () {
    $settingsData = [
        'shipping_methods' => ['standard', 'express'],
        'free_shipping_threshold' => 100,
        'standard_shipping_cost' => 10,
        'express_shipping_cost' => 20,
    ];

    $response = $this->actingAs($this->admin)
        ->put('/admin/settings/shipping', $settingsData);

    $response->assertRedirect();

    foreach ($settingsData as $key => $value) {
        $this->assertDatabaseHas('settings', [
            'key' => $key,
            'value' => is_array($value) ? json_encode($value) : $value,
        ]);
    }
});

test('admin can update email settings', function () {
    $settingsData = [
        'mail_driver' => 'smtp',
        'mail_host' => 'smtp.gmail.com',
        'mail_port' => 587,
        'mail_username' => 'test@gmail.com',
        'mail_password' => 'test_password',
        'mail_encryption' => 'tls',
    ];

    $response = $this->actingAs($this->admin)
        ->put('/admin/settings/email', $settingsData);

    $response->assertRedirect();

    foreach ($settingsData as $key => $value) {
        $this->assertDatabaseHas('settings', [
            'key' => $key,
            'value' => $value,
        ]);
    }
});

test('admin can update SEO settings', function () {
    $settingsData = [
        'meta_title' => 'Test Store - Online Shopping',
        'meta_description' => 'Best online store for all your needs',
        'meta_keywords' => 'online, store, shopping, ecommerce',
        'google_analytics_id' => 'GA-123456789',
    ];

    $response = $this->actingAs($this->admin)
        ->put('/admin/settings/seo', $settingsData);

    $response->assertRedirect();

    foreach ($settingsData as $key => $value) {
        $this->assertDatabaseHas('settings', [
            'key' => $key,
            'value' => $value,
        ]);
    }
});

test('settings are applied to frontend', function () {
    Setting::create([
        'key' => 'site_name',
        'value' => 'My Test Store',
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('My Test Store');
});
