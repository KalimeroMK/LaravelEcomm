# Database Configuration for Multi-Tenant Setup

Ова е пример за database конфигурацијата во `config/database.php` за мултитенант функционалност.

## Додајте ги овие connections во config/database.php

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

## Environment Variables за Database

Додајте ги овие во вашиот `.env` фајл:

```env
# Main Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_ecommerce
DB_USERNAME=root
DB_PASSWORD=your_password

# Owner Database (за tenant metadata)
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

## Database Setup инструкции

1. **Креирајте ги базите на податоци**:

```sql
-- Main application database
CREATE DATABASE laravel_ecommerce;

-- Owner database (за tenant metadata)
CREATE DATABASE owner_db;

-- Tenant template database
CREATE DATABASE tenant_template;
```

2. **Стартувајте ги миграциите**:

```bash
# Мигрирајте ја главната база
php artisan migrate

# Иницијализирајте го tenant системот
php artisan tenants:init
```

3. **Креирајте тест tenant**:

```bash
php artisan tenants:create
```

## Production настройки

За production, користете:

-   **Различни database servers** за owner и tenant бази
-   **SSL connections** за сите database connections
-   **Connection pooling** за подобри перформанси
-   **Backup стратегии** за сите tenant бази
-   **Monitoring** за database перформанси

## Безбедносни забелешки

-   **Користете различни credentials** за owner и tenant бази
-   **Ограничете го пристапот** до owner базата
-   **Користете SSL** за production
-   **Регуларно backup-увајте** ги сите бази
-   **Мониторирајте го пристапот** до базите
