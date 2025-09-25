<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;

uses(RefreshDatabase::class);

test('application health check works', function () {
    $response = $this->get('/health');
    
    $response->assertStatus(200);
    $response->assertJson([
        'status' => 'ok',
        'timestamp' => now()->toISOString()
    ]);
});

test('application version endpoint works', function () {
    $response = $this->get('/version');
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'version',
        'environment',
        'php_version'
    ]);
});

test('maintenance mode can be enabled', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($admin)
        ->post('/admin/maintenance/enable');
    
    $response->assertRedirect();
    
    $response = $this->get('/');
    $response->assertStatus(503);
});

test('maintenance mode can be disabled', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    // Enable maintenance mode
    $this->actingAs($admin)->post('/admin/maintenance/enable');
    
    // Disable maintenance mode
    $response = $this->actingAs($admin)
        ->post('/admin/maintenance/disable');
    
    $response->assertRedirect();
    
    $response = $this->get('/');
    $response->assertStatus(200);
});

test('cache can be cleared', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($admin)
        ->post('/admin/cache/clear');
    
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Cache cleared successfully');
});

test('config cache can be cleared', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($admin)
        ->post('/admin/config/clear');
    
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Config cache cleared successfully');
});

test('route cache can be cleared', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($admin)
        ->post('/admin/route/clear');
    
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Route cache cleared successfully');
});

test('view cache can be cleared', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($admin)
        ->post('/admin/view/clear');
    
    $response->assertRedirect();
    $response->assertSessionHas('success', 'View cache cleared successfully');
});

test('database backup can be created', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($admin)
        ->post('/admin/backup/create');
    
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Backup created successfully');
});

test('system information is displayed', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($admin)
        ->get('/admin/system/info');
    
    $response->assertStatus(200);
    $response->assertSee('System Information');
    $response->assertSee('PHP Version');
    $response->assertSee('Laravel Version');
});

test('log files can be viewed', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($admin)
        ->get('/admin/logs');
    
    $response->assertStatus(200);
    $response->assertSee('Log Files');
});

test('log files can be cleared', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($admin)
        ->post('/admin/logs/clear');
    
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Log files cleared successfully');
});

test('queue status can be checked', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($admin)
        ->get('/admin/queue/status');
    
    $response->assertStatus(200);
    $response->assertSee('Queue Status');
});

test('failed jobs can be viewed', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($admin)
        ->get('/admin/queue/failed');
    
    $response->assertStatus(200);
    $response->assertSee('Failed Jobs');
});

test('failed jobs can be retried', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($admin)
        ->post('/admin/queue/retry-all');
    
    $response->assertRedirect();
    $response->assertSessionHas('success', 'All failed jobs have been retried');
});

test('environment variables can be viewed', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($admin)
        ->get('/admin/environment');
    
    $response->assertStatus(200);
    $response->assertSee('Environment Variables');
});

test('database migrations can be run', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($admin)
        ->post('/admin/migrate');
    
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Migrations run successfully');
});

test('database seeders can be run', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($admin)
        ->post('/admin/seed');
    
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Seeders run successfully');
});
