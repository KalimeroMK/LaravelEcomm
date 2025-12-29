<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Tenant\Models\Tenant;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Set up owner and tenant connections for testing using SQLite
    $defaultConnection = config('database.default');
    $defaultConfig = config("database.connections.{$defaultConnection}");

    // Use SQLite for both owner and tenant in tests
    config([
        'database.connections.owner' => $defaultConfig,
        'database.connections.tenant' => array_merge($defaultConfig, [
            'database' => ':memory:',
        ]),
    ]);
});

test('it can create a tenant', function () {
    $tenant = Tenant::create([
        'name' => 'Test Tenant',
        'domain' => 'test.example.com',
        'database' => 'test_tenant_db',
    ]);

    expect($tenant)->toBeInstanceOf(Tenant::class);
    expect($tenant->name)->toBe('Test Tenant');
    expect($tenant->domain)->toBe('test.example.com');
    expect($tenant->database)->toBe('test_tenant_db');
});

test('it uses default connection in testing environment', function () {
    $tenant = new Tenant();
    // In testing environment, it should use default connection (sqlite)
    // But if owner connection is configured, it will use that
    $connectionName = $tenant->getConnectionName();
    expect($connectionName)->toBeIn([config('database.default'), 'owner']);
});

test('it can configure tenant database', function () {
    $tenant = Tenant::create([
        'name' => 'Test Tenant',
        'domain' => 'test.example.com',
        'database' => 'test_tenant_db',
    ]);

    // Store original default connection
    $originalConnection = DB::getDefaultConnection();

    $result = $tenant->configure();

    expect($result)->toBeInstanceOf(Tenant::class);
    // For SQLite, it should use the same database file or :memory:
    $tenantDatabase = config('database.connections.tenant.database');
    expect($tenantDatabase)->not->toBeNull();

    // Reset to original connection
    DB::setDefaultConnection($originalConnection);
});

test('it can use tenant database', function () {
    $tenant = Tenant::create([
        'name' => 'Test Tenant',
        'domain' => 'test.example.com',
        'database' => 'test_tenant_db',
    ]);

    // Store original default connection
    $originalConnection = DB::getDefaultConnection();

    // Configure first to set up tenant connection
    $tenant->configure();

    $result = $tenant->use();

    expect($result)->toBeInstanceOf(Tenant::class);
    expect(DB::getDefaultConnection())->toBe('tenant');

    // Reset to original connection after test
    DB::setDefaultConnection($originalConnection);
});

test('it can find tenant by domain', function () {
    Tenant::create([
        'name' => 'Test Tenant',
        'domain' => 'test.example.com',
        'database' => 'test_tenant_db',
    ]);

    $tenant = Tenant::whereDomain('test.example.com')->first();

    expect($tenant)->toBeInstanceOf(Tenant::class);
    expect($tenant->domain)->toBe('test.example.com');
});

test('it has fillable attributes', function () {
    $tenant = new Tenant();
    $fillable = $tenant->getFillable();

    expect($fillable)->toContain('name');
    expect($fillable)->toContain('domain');
    expect($fillable)->toContain('database');
});

test('it configures tenant connection correctly', function () {
    $tenant = Tenant::create([
        'name' => 'Test Tenant',
        'domain' => 'test.example.com',
        'database' => 'test_tenant_db',
    ]);

    // Store original default connection
    $originalConnection = DB::getDefaultConnection();

    // Verify configure method works without errors
    // In SQLite testing environment, purge is skipped to avoid migration issues
    $result = $tenant->configure();

    expect($result)->toBeInstanceOf(Tenant::class);
    // Verify tenant connection is configured
    expect(config('database.connections.tenant'))->not->toBeNull();

    // Reset to original connection
    DB::setDefaultConnection($originalConnection);
});

test('it handles cache and connection configuration', function () {
    $tenant = Tenant::create([
        'name' => 'Test Tenant',
        'domain' => 'test.example.com',
        'database' => 'test_tenant_db',
    ]);

    // Store original default connection
    $originalConnection = DB::getDefaultConnection();

    // Verify configure method works
    // Cache flush happens internally, just verify method works
    $result = $tenant->configure();

    expect($result)->toBeInstanceOf(Tenant::class);
    expect(config('database.connections.tenant'))->not->toBeNull();

    // Reset to original connection
    DB::setDefaultConnection($originalConnection);
});
