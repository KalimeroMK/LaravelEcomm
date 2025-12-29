<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Newsletter\Models\Newsletter;

require_once __DIR__.'/../../../TestHelpers.php';

uses(RefreshDatabase::class);

test('user can subscribe to newsletter', function () {
    $subscriptionData = [
        'email' => 'test@example.com',
        'name' => 'Test User',
    ];

    // Use the frontend subscribe route
    $response = $this->post(route('subscribe'), $subscriptionData);

    // The API route returns JSON, not redirect
    expect($response->status())->toBeIn([200, 201, 302]);
    $this->assertDatabaseHas('newsletters', [
        'email' => 'test@example.com',
    ]);
});

test('user can unsubscribe from newsletter', function () {
    $newsletter = Newsletter::factory()->create([
        'email' => 'test@example.com',
        'is_validated' => true,
    ]);

    $response = $this->get(route('email.unsubscribe', ['email' => 'test@example.com']));

    $response->assertStatus(200);
    $this->assertDatabaseHas('newsletters', [
        'email' => 'test@example.com',
        'is_validated' => false,
    ]);
});

test('newsletter analytics page loads', function () {
    $admin = createAdminUser();

    $response = $this->actingAs($admin)
        ->get(route('newsletters.analytics'));

    $response->assertStatus(200);
    $response->assertSee('Newsletter Analytics', false);
});

test('newsletter analytics API returns data', function () {
    $admin = createAdminUser();

    $response = $this->actingAs($admin, 'sanctum')
        ->get(route('api.newsletter.analytics'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'total_sent',
            'total_opened',
            'total_clicked',
            'open_rate',
            'click_rate',
        ],
    ]);
});

test('newsletter export works', function () {
    $admin = createAdminUser();

    $response = $this->actingAs($admin, 'sanctum')
        ->post(route('api.newsletter.analytics.export'), [
            'format' => 'json',
        ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data',
        'format',
    ]);
});
