<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Tenant\Models\Tenant;
use Modules\User\Models\User;
use Tests\TestCase;

class TenantControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up owner connection for testing
        config(['database.connections.owner' => config('database.connections.mysql')]);
        
        // Create admin user for testing
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('admin');
    }

    /** @test */
    public function it_can_display_tenant_index_page()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('tenant.index'));

        $response->assertStatus(200);
        $response->assertViewIs('tenant::index');
    }

    /** @test */
    public function it_can_display_tenant_create_page()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('tenant.create'));

        $response->assertStatus(200);
        $response->assertViewIs('tenant::create');
    }

    /** @test */
    public function it_can_store_a_new_tenant()
    {
        $tenantData = [
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'database' => 'test_tenant_db',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('tenant.store'), $tenantData);

        $response->assertRedirect(route('banners.index'));
        
        $this->assertDatabaseHas('tenants', [
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'database' => 'test_tenant_db',
        ]);
    }

    /** @test */
    public function it_can_display_tenant_edit_page()
    {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'database' => 'test_tenant_db',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('tenant.edit', $tenant));

        $response->assertStatus(200);
        $response->assertViewIs('tenant::edit');
        $response->assertViewHas('tenant', $tenant);
    }

    /** @test */
    public function it_can_update_tenant()
    {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'database' => 'test_tenant_db',
        ]);

        $updateData = [
            'name' => 'Updated Tenant',
            'domain' => 'updated.example.com',
            'database' => 'updated_tenant_db',
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('tenant.update', $tenant), $updateData);

        $response->assertRedirect(route('tenant.edit', $tenant));
        
        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'name' => 'Updated Tenant',
            'domain' => 'updated.example.com',
            'database' => 'updated_tenant_db',
        ]);
    }

    /** @test */
    public function it_can_delete_tenant()
    {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'database' => 'test_tenant_db',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('tenant.destroy', $tenant));

        $response->assertRedirect(route('tenant.index'));
        
        $this->assertDatabaseMissing('tenants', [
            'id' => $tenant->id,
        ]);
    }

    /** @test */
    public function it_requires_authentication_for_tenant_operations()
    {
        $response = $this->get(route('tenant.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('tenant.create'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_requires_admin_role_for_tenant_operations()
    {
        $regularUser = User::factory()->create();
        $regularUser->assignRole('user');

        $response = $this->actingAs($regularUser)
            ->get(route('tenant.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function it_validates_tenant_creation_data()
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('tenant.store'), []);

        $response->assertSessionHasErrors(['name', 'domain', 'database']);
    }

    /** @test */
    public function it_validates_unique_domain()
    {
        Tenant::create([
            'name' => 'Existing Tenant',
            'domain' => 'existing.example.com',
            'database' => 'existing_db',
        ]);

        $duplicateData = [
            'name' => 'New Tenant',
            'domain' => 'existing.example.com', // Duplicate domain
            'database' => 'new_db',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('tenant.store'), $duplicateData);

        $response->assertSessionHasErrors(['domain']);
    }

    /** @test */
    public function it_validates_unique_database()
    {
        Tenant::create([
            'name' => 'Existing Tenant',
            'domain' => 'existing.example.com',
            'database' => 'existing_db',
        ]);

        $duplicateData = [
            'name' => 'New Tenant',
            'domain' => 'new.example.com',
            'database' => 'existing_db', // Duplicate database
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('tenant.store'), $duplicateData);

        $response->assertSessionHasErrors(['database']);
    }
}
