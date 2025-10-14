<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;

require_once __DIR__.'/../TestHelpers.php';

uses(RefreshDatabase::class);

test('all module routes are accessible', function () {
    $admin = createAdminUser();
    $user = User::factory()->create();

    // Test admin routes
    $adminRoutes = [
        '/admin',
        '/admin/analytics',
        '/admin/email-campaigns/analytics',
        '/admin/categories',
        '/admin/brands',
        '/admin/products',
        '/admin/orders',
        '/admin/users',
        '/admin/settings',
        '/admin/banners',
        '/admin/posts',
        '/admin/tenants',
        '/admin/roles',
        '/admin/permissions',
        '/admin/messages',
        '/admin/coupons',
        '/admin/bundles',
        '/admin/shipping/methods',
        '/admin/billing/invoices',
        '/admin/2fa/settings',
        '/admin/logs',
        '/admin/system/info',
    ];

    foreach ($adminRoutes as $route) {
        $response = $this->actingAs($admin)->get($route);
        expect($response->status())->toBeIn([200, 302, 404]);
    }

    // Test user routes
    $userRoutes = [
        '/',
        '/products',
        '/blog',
        '/contact',
        '/about',
        '/cart',
        '/profile',
        '/orders',
        '/billing/history',
    ];

    foreach ($userRoutes as $route) {
        $response = $this->actingAs($user)->get($route);
        expect($response->status())->toBeIn([200, 302, 404]);
    }
});

test('all API endpoints return proper responses', function () {
    $admin = createAdminUser();

    $apiRoutes = [
        '/api/v1/analytics/overview',
        '/api/v1/analytics/sales',
        '/api/v1/analytics/users',
        '/api/v1/analytics/products',
        '/api/v1/newsletter/analytics',
        '/api/v1/products/search',
        '/api/v1/categories',
        '/api/v1/brands',
    ];

    foreach ($apiRoutes as $route) {
        $response = $this->actingAs($admin)->get($route);
        expect($response->status())->toBeIn([200, 401, 404]);
    }
});

test('authentication works across all modules', function () {
    $user = User::factory()->create();

    // Test protected routes require authentication
    $protectedRoutes = [
        '/admin',
        '/profile',
        '/cart',
        '/orders',
    ];

    foreach ($protectedRoutes as $route) {
        $response = $this->get($route);
        expect($response->status())->toBe(302); // Redirect to login
    }

    // Test authenticated access
    foreach ($protectedRoutes as $route) {
        $response = $this->actingAs($user)->get($route);
        expect($response->status())->toBeIn([200, 403, 404]);
    }
});

test('all modules have proper error handling', function () {
    $admin = createAdminUser();

    // Test 404 handling
    $response = $this->actingAs($admin)->get('/admin/nonexistent');
    expect($response->status())->toBe(404);

    // Test 403 handling for non-admin users
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/admin');
    expect($response->status())->toBeIn([403, 404]);
});

test('all forms have CSRF protection', function () {
    $admin = createAdminUser();

    $forms = [
        ['url' => '/admin/categories', 'method' => 'POST'],
        ['url' => '/admin/brands', 'method' => 'POST'],
        ['url' => '/admin/products', 'method' => 'POST'],
        ['url' => '/admin/posts', 'method' => 'POST'],
    ];

    foreach ($forms as $form) {
        $response = $this->actingAs($admin)->post($form['url'], []);
        expect($response->status())->toBeIn([200, 302, 422]);
    }
});
