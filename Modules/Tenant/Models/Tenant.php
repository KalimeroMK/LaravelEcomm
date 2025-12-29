<?php

declare(strict_types=1);

namespace Modules\Tenant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Tenant\Database\Factories\TenantFactory;

/**
 * @property int $id
 * @property string $name
 * @property string $domain
 * @property string $database Name of the tenant's database.
 */
class Tenant extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'domain', 'database'];

    /**
     * Get the database connection name for the model.
     * In test environment, use default connection. Otherwise use 'owner' connection.
     */
    public function getConnectionName(): ?string
    {
        if (app()->environment('testing')) {
            return config('database.default');
        }

        // Check if 'owner' connection is configured
        if (config('database.connections.owner')) {
            return 'owner';
        }

        // Fallback to default connection
        return config('database.default');
    }

    /**
     * Configure the tenant's database connection dynamically.
     */
    public function configure(): self
    {
        $defaultConnection = config('database.default');
        $defaultConfig = config("database.connections.{$defaultConnection}");
        $driver = $defaultConfig['driver'] ?? 'sqlite';

        // For SQLite, use the same database file but with different connection name
        // For MySQL/MariaDB, use the tenant's database name
        if ($driver === 'sqlite') {
            // SQLite uses the same database file for all tenants
            config([
                'database.connections.tenant' => array_merge($defaultConfig, [
                    'database' => $defaultConfig['database'] ?? ':memory:',
                ]),
            ]);
        } else {
            // For MySQL/MariaDB, use the tenant's specific database
            config([
                'database.connections.tenant' => array_merge($defaultConfig, [
                    'database' => $this->database,
                ]),
            ]);
        }

        // Purge the 'tenant' connection to refresh its settings
        // In testing environment with SQLite, skip purge to avoid migration issues
        // SQLite in-memory databases don't need purging in tests
        if (! (app()->environment('testing') && $driver === 'sqlite')) {
            DB::purge('tenant');
        }

        // Consider scoping the cache flush if possible
        Cache::flush();

        return $this;
    }

    /**
     * Activate the tenant context across the application.
     */
    public function use(): self
    {
        // Set the default database connection to 'tenant'
        DB::setDefaultConnection('tenant');

        return $this;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return TenantFactory::new();
    }
}
