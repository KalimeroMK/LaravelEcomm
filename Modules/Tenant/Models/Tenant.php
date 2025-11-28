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
        // Update the configuration for the tenant connection dynamically
        config([
            'database.connections.tenant.database' => $this->database,
        ]);

        // Purge the 'tenant' connection to refresh its settings
        DB::purge('tenant');

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
