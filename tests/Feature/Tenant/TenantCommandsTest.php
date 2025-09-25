<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Modules\Tenant\Models\Tenant;
use Tests\TestCase;

class TenantCommandsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up owner connection for testing
        config(['database.connections.owner' => config('database.connections.mysql')]);
        config(['database.connections.tenant' => config('database.connections.mysql')]);
    }

    /** @test */
    public function it_can_run_tenant_init_command()
    {
        $this->artisan('tenants:init')
            ->expectsOutput('Running migration from: Modules/Tenant/Database/Migrations/Owner')
            ->expectsOutput('Migrations have been executed successfully.')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_can_create_tenant_via_command()
    {
        // Mock the database existence check
        DB::shouldReceive('connection')
            ->with('owner')
            ->andReturnSelf();
        
        DB::shouldReceive('select')
            ->andReturn([]); // No existing database
        
        DB::shouldReceive('statement')
            ->with('CREATE DATABASE `test_tenant_db`')
            ->andReturn(true);

        $this->artisan('tenants:create')
            ->expectsQuestion('What is the tenant\'s name?', 'Test Tenant')
            ->expectsQuestion('What is the tenant\'s domain?', 'test.example.com')
            ->expectsQuestion('What is the tenant\'s database name?', 'test_tenant_db')
            ->expectsOutput('Tenant Test Tenant created successfully with domain test.example.com and database test_tenant_db.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('tenants', [
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'database' => 'test_tenant_db',
        ]);
    }

    /** @test */
    public function it_validates_tenant_creation_input()
    {
        $this->artisan('tenants:create')
            ->expectsQuestion('What is the tenant\'s name?', '') // Empty name
            ->expectsQuestion('What is the tenant\'s domain?', 'invalid-domain') // Invalid domain
            ->expectsQuestion('What is the tenant\'s database name?', '') // Empty database
            ->expectsOutput('Tenant not created. See error messages below:')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_prevents_duplicate_tenant_creation()
    {
        // Create existing tenant
        Tenant::create([
            'name' => 'Existing Tenant',
            'domain' => 'existing.example.com',
            'database' => 'existing_db',
        ]);

        $this->artisan('tenants:create')
            ->expectsQuestion('What is the tenant\'s name?', 'New Tenant')
            ->expectsQuestion('What is the tenant\'s domain?', 'existing.example.com') // Duplicate domain
            ->expectsQuestion('What is the tenant\'s database name?', 'new_db')
            ->expectsOutput('Tenant not created. See error messages below:')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_can_migrate_specific_tenant()
    {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'database' => 'test_tenant_db',
        ]);

        // Mock tenant configuration
        $tenant->shouldReceive('configure')->andReturnSelf();
        $tenant->shouldReceive('use')->andReturnSelf();

        $this->artisan('tenants:migrate', ['tenant' => $tenant->id])
            ->expectsOutput('-----------------------------------------')
            ->expectsOutput("Migrating Tenant #{$tenant->id} (Test Tenant)")
            ->expectsOutput('-----------------------------------------')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_can_migrate_all_tenants()
    {
        Tenant::create([
            'name' => 'Tenant 1',
            'domain' => 'tenant1.example.com',
            'database' => 'tenant1_db',
        ]);

        Tenant::create([
            'name' => 'Tenant 2',
            'domain' => 'tenant2.example.com',
            'database' => 'tenant2_db',
        ]);

        $this->artisan('tenants:migrate')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_can_migrate_with_fresh_option()
    {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'database' => 'test_tenant_db',
        ]);

        $this->artisan('tenants:migrate', [
            'tenant' => $tenant->id,
            '--fresh' => true,
        ])
            ->assertExitCode(0);
    }

    /** @test */
    public function it_can_migrate_with_seed_option()
    {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'database' => 'test_tenant_db',
        ]);

        $this->artisan('tenants:migrate', [
            'tenant' => $tenant->id,
            '--seed' => true,
        ])
            ->assertExitCode(0);
    }

    /** @test */
    public function it_handles_nonexistent_tenant_migration()
    {
        $this->artisan('tenants:migrate', ['tenant' => 999])
            ->expectsOutput('Tenant with ID 999 not found.')
            ->assertExitCode(0);
    }
}
