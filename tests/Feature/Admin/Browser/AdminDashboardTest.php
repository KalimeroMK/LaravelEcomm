<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;

require_once __DIR__ . '/../../../TestHelpers.php';

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = createAdminUser([
        'name' => 'Admin User',
    ]);
});

test('admin dashboard loads successfully', function () {
    $response = $this->actingAs($this->user)
        ->get('/admin');

    $response->assertStatus(200);
    $response->assertSee('Dashboard');
});

test('admin analytics dashboard loads', function () {
    $response = $this->actingAs($this->user)
        ->get('/admin/analytics');

    $response->assertStatus(200);
    $response->assertSee('Analytics Dashboard');
});

test('admin email analytics loads', function () {
    $response = $this->actingAs($this->user)
        ->get('/admin/email-campaigns/analytics');

    $response->assertStatus(200);
});

test('admin sidebar navigation works', function () {
    $response = $this->actingAs($this->user)
        ->get('/admin');

    $response->assertSee('Email Marketing');
    $response->assertSee('Analytics Dashboard');
    $response->assertSee('Email Analytics');
});

test('admin user can access admin routes', function () {
    $response = $this->actingAs($this->user)
        ->get('/admin');

    $response->assertStatus(200);
});

test('non-admin user cannot access admin routes', function () {
    $regularUser = User::factory()->create();
    
    $response = $this->actingAs($regularUser)
        ->get('/admin');

    $response->assertStatus(403);
});
