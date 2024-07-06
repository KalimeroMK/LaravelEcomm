<?php

namespace Modules\Tenant\Models;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Core;

/**
 * Class Tenant
 *
 * @property int $id
 * @property string $name
 * @property string $domain
 * @property string $database
 */
class Tenant extends Core
{

    protected $fillable = [
        'name',
        'domain',
        'database',
    ];

    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'owner';

    /**
     * The database name for the tenant.
     *
     * @var string
     */
    public $database;

    protected $guarded = [];

    /**
     * Configure the tenant's database connection.
     *
     * @return static
     */
    public function configure(): static
    {
        config([
            'database.connections.mysql.database' => $this->database,
        ]);

        DB::purge('mysql');

        app('cache')->purge(config('cache.default'));

        return $this;
    }

    /**
     * Set the tenant instance in the application.
     *
     * @return static
     */
    public function use(): static
    {
        app()->forgetInstance('mysql');

        app()->instance('mysql', $this);

        return $this;
    }
}
