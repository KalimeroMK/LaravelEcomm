# Multi-Tenant Environment Configuration Example

Ова е пример за `.env` фајлот со сите потребни настройки за мултитенант функционалноста.

## Основна конфигурација

```env
# Application Configuration
APP_NAME="Laravel Ecommerce"
APP_ENV=local
APP_KEY=base64:your-app-key-here
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost
```

## Database конфигурација

```env
# Main Application Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_ecommerce
DB_USERNAME=root
DB_PASSWORD=your_password

# Multi-Tenant Configuration
MULTI_TENANT_ENABLED=true
TENANT_MAIN_DOMAIN=localhost
TENANT_OWNER_CONNECTION=owner
TENANT_DEFAULT_CONNECTION=tenant

# Owner Database (за tenant metadata)
OWNER_DB_CONNECTION=owner
OWNER_DB_HOST=127.0.0.1
OWNER_DB_PORT=3306
OWNER_DB_DATABASE=owner_db
OWNER_DB_USERNAME=root
OWNER_DB_PASSWORD=your_password

# Tenant Database Template
TENANT_DB_CONNECTION=tenant
TENANT_DB_HOST=127.0.0.1
TENANT_DB_PORT=3306
TENANT_DB_DATABASE=tenant_template
TENANT_DB_USERNAME=root
TENANT_DB_PASSWORD=your_password
```

## Tenant специфични настройки

```env
# Tenant Database Naming
TENANT_DB_PREFIX=tenant_
TENANT_DB_SUFFIX=
TENANT_DB_SEPARATOR=_

# Tenant Domain Configuration
TENANT_SUBDOMAIN_SEPARATOR=.
TENANT_WILDCARD_DOMAIN=false

# Tenant Cache Configuration
TENANT_CACHE_PREFIX=tenant_
TENANT_CACHE_TTL=3600
TENANT_CACHE_STORE=default

# Tenant Session Configuration
TENANT_ISOLATE_SESSIONS=true
TENANT_SESSION_PREFIX=tenant_

# Tenant Queue Configuration
TENANT_QUEUE_AWARE=true
TENANT_QUEUE_PREFIX=tenant_

# Tenant Storage Configuration
TENANT_ISOLATE_FILES=true
TENANT_STORAGE_DISK=local
TENANT_STORAGE_PATH=tenants

# Tenant Middleware Configuration
TENANT_MIDDLEWARE_DETECTION=true
TENANT_AUTO_SWITCH=true
TENANT_FALLBACK_TENANT=

# Tenant Security Configuration
TENANT_ISOLATE_USERS=true
TENANT_ISOLATE_PERMISSIONS=true
TENANT_CROSS_ACCESS=false
```

## Дополнителни настройки

```env
# Cache Configuration
CACHE_DRIVER=file
CACHE_PREFIX=

# Session Configuration
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# Queue Configuration
QUEUE_CONNECTION=sync
QUEUE_FAILED_DRIVER=database-uuids

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Redis Configuration (ако користиш Redis)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Logging Configuration
LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Security Configuration
BCRYPT_ROUNDS=12
JWT_SECRET=your-jwt-secret
JWT_TTL=60
JWT_REFRESH_TTL=20160

# Rate Limiting
RATE_LIMIT_ENABLED=true
RATE_LIMIT_ATTEMPTS=60
RATE_LIMIT_DECAY_MINUTES=1
```

## Database конфигурација во config/database.php

Додајте ги овие connections во вашиот `config/database.php`:

```php
'connections' => [
    // ... existing connections ...

    'owner' => [
        'driver' => 'mysql',
        'host' => env('OWNER_DB_HOST', '127.0.0.1'),
        'port' => env('OWNER_DB_PORT', '3306'),
        'database' => env('OWNER_DB_DATABASE', 'owner_db'),
        'username' => env('OWNER_DB_USERNAME', 'forge'),
        'password' => env('OWNER_DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => null,
    ],

    'tenant' => [
        'driver' => 'mysql',
        'host' => env('TENANT_DB_HOST', '127.0.0.1'),
        'port' => env('TENANT_DB_PORT', '3306'),
        'database' => env('TENANT_DB_DATABASE', 'tenant_template'),
        'username' => env('TENANT_DB_USERNAME', 'forge'),
        'password' => env('TENANT_DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => null,
    ],
],
```

## Setup инструкции

1. **Копирајте го овој пример во `.env` фајлот**
2. **Ажурирајте ги database настройките** според вашата конфигурација
3. **Додајте ги database connections** во `config/database.php`
4. **Стартувајте ги командите**:

```bash
# Иницијализирајте го tenant системот
php artisan tenants:init

# Креирајте нов tenant
php artisan tenants:create

# Мигрирајте ги tenant базите
php artisan tenants:migrate
```

## Пример за production настройки

За production, променете ги овие вредности:

```env
APP_ENV=production
APP_DEBUG=false
MULTI_TENANT_ENABLED=true
TENANT_MAIN_DOMAIN=yourdomain.com
TENANT_ISOLATE_SESSIONS=true
TENANT_ISOLATE_FILES=true
TENANT_ISOLATE_USERS=true
TENANT_ISOLATE_PERMISSIONS=true
TENANT_CROSS_ACCESS=false
```

## Безбедносни забелешки

-   **Никогаш не комитирајте `.env` фајл** во git
-   **Користете различни passwords** за различни environments
-   **Овозможете SSL** за production
-   **Конфигурирајте го firewall** за database пристап
-   **Регуларно backup-увајте** ги tenant базите
