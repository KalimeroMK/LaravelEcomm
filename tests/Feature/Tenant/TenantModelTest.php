<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Tenant\Models\Tenant;
use Tests\TestCase;

class TenantModelTest extends TestCase
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
    public function it_can_create_a_tenant()
    {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'database' => 'test_tenant_db',
        ]);

        $this->assertInstanceOf(Tenant::class, $tenant);
        $this->assertEquals('Test Tenant', $tenant->name);
        $this->assertEquals('test.example.com', $tenant->domain);
        $this->assertEquals('test_tenant_db', $tenant->database);
    }

    /** @test */
    public function it_uses_owner_connection_by_default()
    {
        $tenant = new Tenant();
        $this->assertEquals('owner', $tenant->getConnectionName());
    }

    /** @test */
    public function it_can_configure_tenant_database()
    {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'database' => 'test_tenant_db',
        ]);

        $result = $tenant->configure();

        $this->assertInstanceOf(Tenant::class, $result);
        $this->assertEquals('test_tenant_db', config('database.connections.tenant.database'));
    }

    /** @test */
    public function it_can_use_tenant_database()
    {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'database' => 'test_tenant_db',
        ]);

        $result = $tenant->use();

        $this->assertInstanceOf(Tenant::class, $result);
        $this->assertEquals('tenant', DB::getDefaultConnection());
    }

    /** @test */
    public function it_can_find_tenant_by_domain()
    {
        Tenant::create([
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'database' => 'test_tenant_db',
        ]);

        $tenant = Tenant::whereDomain('test.example.com')->first();

        $this->assertInstanceOf(Tenant::class, $tenant);
        $this->assertEquals('test.example.com', $tenant->domain);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $tenant = new Tenant();
        $fillable = $tenant->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('domain', $fillable);
        $this->assertContains('database', $fillable);
    }

    /** @test */
    public function it_purges_tenant_connection_when_configured()
    {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'database' => 'test_tenant_db',
        ]);

        // Mock DB::purge to verify it's called
        DB::shouldReceive('purge')->with('tenant')->once();

        $tenant->configure();
    }

    /** @test */
    public function it_flushes_cache_when_configured()
    {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'database' => 'test_tenant_db',
        ]);

        // Mock Cache::flush to verify it's called
        Cache::shouldReceive('flush')->once();

        $tenant->configure();
    }
}
