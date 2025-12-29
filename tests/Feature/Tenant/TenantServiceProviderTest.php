<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Modules\Tenant\Models\Tenant;
use Modules\Tenant\Providers\TenantServiceProvider;
use Tests\TestCase;

class TenantServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up owner connection for testing using SQLite
        $defaultConnection = config('database.default');
        $defaultConfig = config("database.connections.{$defaultConnection}");

        config(['database.connections.owner' => $defaultConfig]);
    }

    /** @test */
    public function it_registers_tenant_service_provider_when_enabled()
    {
        Config::set('tenant.multi_tenant.enabled', true);

        $provider = new TenantServiceProvider($this->app);
        $provider->register();

        $this->assertTrue($this->app->bound('tenant'));
    }

    /** @test */
    public function it_does_not_register_when_disabled()
    {
        Config::set('tenant.multi_tenant.enabled', false);

        $provider = new TenantServiceProvider($this->app);
        $provider->register();

        $this->assertFalse($this->app->bound('tenant'));
    }

    /** @test */
    public function it_configures_tenant_based_on_domain()
    {
        Config::set('tenant.multi_tenant.enabled', true);

        // Create a tenant
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'database' => 'test_tenant_db',
        ]);

        // Mock request with specific host
        $this->app['request'] = $this->app['request']->duplicate(
            null,
            null,
            null,
            null,
            null,
            ['HTTP_HOST' => 'test.example.com']
        );

        $provider = new TenantServiceProvider($this->app);
        $provider->boot();

        // Verify tenant is configured
        $this->assertEquals('test_tenant_db', config('database.connections.tenant.database'));
    }

    /** @test */
    public function it_handles_tenant_not_found_gracefully()
    {
        Config::set('tenant.multi_tenant.enabled', true);

        // Mock request with non-existent domain
        $this->app['request'] = $this->app['request']->duplicate(
            null,
            null,
            null,
            null,
            null,
            ['HTTP_HOST' => 'nonexistent.example.com']
        );

        $provider = new TenantServiceProvider($this->app);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $provider->boot();
    }

    /** @test */
    public function it_skips_tenant_configuration_in_console()
    {
        Config::set('tenant.multi_tenant.enabled', true);

        // Mock console environment
        $this->app->shouldReceive('runningInConsole')->andReturn(true);

        $provider = new TenantServiceProvider($this->app);
        $provider->boot();

        // Should not throw exception
        $this->assertTrue(true);
    }

    /** @test */
    public function it_registers_tenant_commands_when_enabled()
    {
        Config::set('tenant.multi_tenant.enabled', true);

        $provider = new TenantServiceProvider($this->app);
        $provider->boot();

        // Verify commands are registered
        $this->assertTrue(Artisan::has('tenants:create'));
        $this->assertTrue(Artisan::has('tenants:init'));
        $this->assertTrue(Artisan::has('tenants:migrate'));
    }

    /** @test */
    public function it_does_not_register_commands_when_disabled()
    {
        Config::set('tenant.multi_tenant.enabled', false);

        $provider = new TenantServiceProvider($this->app);
        $provider->boot();

        // Commands should not be available
        $this->assertFalse(Artisan::has('tenants:create'));
        $this->assertFalse(Artisan::has('tenants:init'));
        $this->assertFalse(Artisan::has('tenants:migrate'));
    }

    /** @test */
    public function it_configures_queue_for_tenant_awareness()
    {
        Config::set('tenant.multi_tenant.enabled', true);

        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'database' => 'test_tenant_db',
        ]);

        $this->app->instance('tenant', $tenant);

        $provider = new TenantServiceProvider($this->app);
        $provider->boot();

        // Verify queue payload includes tenant_id
        $payload = app('queue')->createPayloadUsing(function () {
            return [];
        });

        $this->assertArrayHasKey('tenant_id', $payload);
        $this->assertEquals($tenant->id, $payload['tenant_id']);
    }

    /** @test */
    public function it_handles_queue_without_tenant()
    {
        Config::set('tenant.multi_tenant.enabled', true);

        $provider = new TenantServiceProvider($this->app);
        $provider->boot();

        // Verify queue payload is empty when no tenant
        $payload = app('queue')->createPayloadUsing(function () {
            return [];
        });

        $this->assertEmpty($payload);
    }
}
