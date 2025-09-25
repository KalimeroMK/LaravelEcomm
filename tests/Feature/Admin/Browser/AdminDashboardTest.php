<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'admin@test.com',
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
    $response->assertSee('Email Analytics');
});

test('admin sidebar navigation works', function () {
    $response = $this->actingAs($this->user)
        ->get('/admin');

    $response->assertSee('Email Marketing');
    $response->assertSee('Analytics Dashboard');
    $response->assertSee('Email Analytics');
});
