<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Multi-Tenancy Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file controls the multi-tenancy features of the
    | application. You can enable/disable multi-tenancy and configure
    | various tenant-related settings.
    |
    */

    'multi_tenant' => [
        'enabled' => env('MULTI_TENANT_ENABLED', false),
        'default_connection' => env('TENANT_DEFAULT_CONNECTION', 'tenant'),
        'owner_connection' => env('TENANT_OWNER_CONNECTION', 'owner'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant Database Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for tenant database connections. Each tenant gets its
    | own database with the same schema.
    |
    */

    'database' => [
        'prefix' => env('TENANT_DB_PREFIX', 'tenant_'),
        'suffix' => env('TENANT_DB_SUFFIX', ''),
        'separator' => env('TENANT_DB_SEPARATOR', '_'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant Domain Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for tenant domain handling and subdomain detection.
    |
    */

    'domain' => [
        'main_domain' => env('TENANT_MAIN_DOMAIN', 'localhost'),
        'subdomain_separator' => env('TENANT_SUBDOMAIN_SEPARATOR', '.'),
        'wildcard_domain' => env('TENANT_WILDCARD_DOMAIN', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Cache configuration for tenant-specific data and settings.
    |
    */

    'cache' => [
        'prefix' => env('TENANT_CACHE_PREFIX', 'tenant_'),
        'ttl' => env('TENANT_CACHE_TTL', 3600), // 1 hour
        'store' => env('TENANT_CACHE_STORE', 'default'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant Session Configuration
    |--------------------------------------------------------------------------
    |
    | Session configuration for tenant isolation.
    |
    */

    'session' => [
        'isolate_sessions' => env('TENANT_ISOLATE_SESSIONS', true),
        'prefix' => env('TENANT_SESSION_PREFIX', 'tenant_'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Queue configuration for tenant-aware job processing.
    |
    */

    'queue' => [
        'tenant_aware' => env('TENANT_QUEUE_AWARE', true),
        'prefix' => env('TENANT_QUEUE_PREFIX', 'tenant_'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant Storage Configuration
    |--------------------------------------------------------------------------
    |
    | Storage configuration for tenant-specific files and media.
    |
    */

    'storage' => [
        'isolate_files' => env('TENANT_ISOLATE_FILES', true),
        'disk' => env('TENANT_STORAGE_DISK', 'local'),
        'path' => env('TENANT_STORAGE_PATH', 'tenants'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant Middleware Configuration
    |--------------------------------------------------------------------------
    |
    | Middleware configuration for tenant detection and switching.
    |
    */

    'middleware' => [
        'tenant_detection' => env('TENANT_MIDDLEWARE_DETECTION', true),
        'auto_switch' => env('TENANT_AUTO_SWITCH', true),
        'fallback_tenant' => env('TENANT_FALLBACK_TENANT', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant Security Configuration
    |--------------------------------------------------------------------------
    |
    | Security configuration for tenant isolation and access control.
    |
    */

    'security' => [
        'isolate_users' => env('TENANT_ISOLATE_USERS', true),
        'isolate_permissions' => env('TENANT_ISOLATE_PERMISSIONS', true),
        'cross_tenant_access' => env('TENANT_CROSS_ACCESS', false),
    ],
];
