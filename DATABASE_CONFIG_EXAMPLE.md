# Database Configuration for Multi-Tenant Setup

This is an example database configuration in `config/database.php` for multi-tenant functionality.

## Add these connections to config/database.php

```php
<?php

return [
    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [
        // Main application database
        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel_ecommerce'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        // Owner database - stores tenant metadata
        'owner' => [
            'driver' => 'mysql',
            'url' => env('OWNER_DATABASE_URL'),
            'host' => env('OWNER_DB_HOST', '127.0.0.1'),
            'port' => env('OWNER_DB_PORT', '3306'),
            'database' => env('OWNER_DB_DATABASE', 'owner_db'),
            'username' => env('OWNER_DB_USERNAME', 'forge'),
            'password' => env('OWNER_DB_PASSWORD', ''),
            'unix_socket' => env('OWNER_DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        // Tenant database template - used for tenant databases
        'tenant' => [
            'driver' => 'mysql',
            'url' => env('TENANT_DATABASE_URL'),
            'host' => env('TENANT_DB_HOST', '127.0.0.1'),
            'port' => env('TENANT_DB_PORT', '3306'),
            'database' => env('TENANT_DB_DATABASE', 'tenant_template'),
            'username' => env('TENANT_DB_USERNAME', 'forge'),
            'password' => env('TENANT_DB_PASSWORD', ''),
            'unix_socket' => env('TENANT_DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        // SQLite for testing
        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        // PostgreSQL
        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'laravel_ecommerce'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],
    ],

    'migrations' => 'migrations',

    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],
    ],
];
```

## Environment Variables for Database

Add these to your `.env` file:

```env
# Main Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_ecommerce
DB_USERNAME=root
DB_PASSWORD=your_password

# Owner Database (for tenant metadata)
OWNER_DB_HOST=127.0.0.1
OWNER_DB_PORT=3306
OWNER_DB_DATABASE=owner_db
OWNER_DB_USERNAME=root
OWNER_DB_PASSWORD=your_password

# Tenant Database Template
TENANT_DB_HOST=127.0.0.1
TENANT_DB_PORT=3306
TENANT_DB_DATABASE=tenant_template
TENANT_DB_USERNAME=root
TENANT_DB_PASSWORD=your_password
```

## Database Setup Instructions

1. **Create the databases**:

```sql
-- Main application database
CREATE DATABASE laravel_ecommerce;

-- Owner database (for tenant metadata)
CREATE DATABASE owner_db;

-- Tenant template database
CREATE DATABASE tenant_template;
```

2. **Run the migrations**:

```bash
# Migrate main database
php artisan migrate

# Initialize tenant system
php artisan tenants:init
```

3. **Create test tenant**:

```bash
php artisan tenants:create
```

## Production Settings

For production, use:

-   **Different database servers** for owner and tenant databases
-   **SSL connections** for all database connections
-   **Connection pooling** for better performance
-   **Backup strategies** for all tenant databases
-   **Monitoring** for database performance

## Security Notes

-   **Use different credentials** for owner and tenant databases
-   **Restrict access** to owner database
-   **Use SSL** for production
-   **Regularly backup** all databases
-   **Monitor access** to databases
