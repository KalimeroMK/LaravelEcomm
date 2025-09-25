<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;
use Modules\Newsletter\Models\Newsletter;

uses(RefreshDatabase::class);

test('user can subscribe to newsletter', function () {
    $subscriptionData = [
        'email' => 'test@example.com',
        'name' => 'Test User'
    ];
    
    $response = $this->post('/newsletter/subscribe', $subscriptionData);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('newsletters', [
        'email' => 'test@example.com'
    ]);
});

test('user can unsubscribe from newsletter', function () {
    $newsletter = Newsletter::factory()->create([
        'email' => 'test@example.com',
        'is_validated' => true
    ]);
    
    $response = $this->post('/newsletter/unsubscribe', [
        'email' => 'test@example.com'
    ]);
    
    $response->assertRedirect();
    $newsletter->refresh();
    expect($newsletter->is_validated)->toBeFalse();
});

test('newsletter analytics page loads', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($admin)
        ->get('/admin/email-campaigns/analytics');
    
    $response->assertStatus(200);
    $response->assertSee('Email Analytics');
});

test('newsletter analytics API returns data', function () {
    Newsletter::factory()->count(5)->create();
    
    $response = $this->post('/api/v1/newsletter/analytics', [
        'start_date' => now()->subMonth()->format('Y-m-d'),
        'end_date' => now()->format('Y-m-d')
    ]);
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'overview',
            'subscribers',
            'segments',
            'performance',
            'campaign_types',
            'campaigns'
        ]
    ]);
});

test('newsletter export works', function () {
    Newsletter::factory()->count(3)->create();
    
    $response = $this->post('/api/v1/newsletter/analytics/export', [
        'format' => 'json',
        'start_date' => now()->subMonth()->format('Y-m-d'),
        'end_date' => now()->format('Y-m-d')
    ]);
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data',
        'format'
    ]);
});
