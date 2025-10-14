<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Tenant\Models\Tenant;

require_once __DIR__.'/../../../TestHelpers.php';

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = createAdminUser();
});

test('admin can view tenants list', function () {
    Tenant::factory()->count(5)->create();

    $response = $this->actingAs($this->admin)
        ->get('/admin/tenants');

    $response->assertStatus(200);
    $response->assertSee('Tenants');
});

test('admin can create tenant', function () {
    $tenantData = [
        'name' => 'Test Store',
        'domain' => 'teststore.com',
        'database_name' => 'teststore_db',
        'is_active' => true,
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/tenants', $tenantData);

    $response->assertRedirect();
    $this->assertDatabaseHas('tenants', [
        'name' => 'Test Store',
        'domain' => 'teststore.com',
    ]);
});

test('admin can edit tenant', function () {
    $tenant = Tenant::factory()->create();

    $response = $this->actingAs($this->admin)
        ->get("/admin/tenants/{$tenant->id}/edit");

    $response->assertStatus(200);
    $response->assertSee($tenant->name);
});

test('admin can update tenant', function () {
    $tenant = Tenant::factory()->create([
        'name' => 'Old Name',
    ]);

    $response = $this->actingAs($this->admin)
        ->put("/admin/tenants/{$tenant->id}", [
            'name' => 'Updated Name',
            'domain' => 'updated.com',
            'is_active' => true,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('tenants', [
        'id' => $tenant->id,
        'name' => 'Updated Name',
        'domain' => 'updated.com',
    ]);
});

test('admin can activate tenant', function () {
    $tenant = Tenant::factory()->create([
        'is_active' => false,
    ]);

    $response = $this->actingAs($this->admin)
        ->put("/admin/tenants/{$tenant->id}/activate");

    $response->assertRedirect();
    $this->assertDatabaseHas('tenants', [
        'id' => $tenant->id,
        'is_active' => true,
    ]);
});

test('admin can deactivate tenant', function () {
    $tenant = Tenant::factory()->create([
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->admin)
        ->put("/admin/tenants/{$tenant->id}/deactivate");

    $response->assertRedirect();
    $this->assertDatabaseHas('tenants', [
        'id' => $tenant->id,
        'is_active' => false,
    ]);
});

test('admin can delete tenant', function () {
    $tenant = Tenant::factory()->create();

    $response = $this->actingAs($this->admin)
        ->delete("/admin/tenants/{$tenant->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('tenants', [
        'id' => $tenant->id,
    ]);
});

test('tenant domain validation works', function () {
    $response = $this->actingAs($this->admin)
        ->post('/admin/tenants', [
            'name' => 'Test Store',
            'domain' => 'invalid-domain',
            'database_name' => 'test_db',
        ]);

    $response->assertSessionHasErrors(['domain']);
});

test('tenant database name validation works', function () {
    $response = $this->actingAs($this->admin)
        ->post('/admin/tenants', [
            'name' => 'Test Store',
            'domain' => 'teststore.com',
            'database_name' => 'invalid-db-name!',
        ]);

    $response->assertSessionHasErrors(['database_name']);
});

test('tenant can be accessed via domain', function () {
    $tenant = Tenant::factory()->create([
        'domain' => 'teststore.com',
        'is_active' => true,
    ]);

    $response = $this->get('http://teststore.com');

    $response->assertStatus(200);
});

test('inactive tenant cannot be accessed', function () {
    $tenant = Tenant::factory()->create([
        'domain' => 'inactive.com',
        'is_active' => false,
    ]);

    $response = $this->get('http://inactive.com');

    $response->assertStatus(404);
});

test('admin can view tenant analytics', function () {
    $tenant = Tenant::factory()->create();

    $response = $this->actingAs($this->admin)
        ->get("/admin/tenants/{$tenant->id}/analytics");

    $response->assertStatus(200);
    $response->assertSee('Analytics');
});

test('admin can manage tenant users', function () {
    $tenant = Tenant::factory()->create();

    $response = $this->actingAs($this->admin)
        ->get("/admin/tenants/{$tenant->id}/users");

    $response->assertStatus(200);
    $response->assertSee('Users');
});
