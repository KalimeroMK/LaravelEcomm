<?php

namespace Modules\Tenant\Models;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Core;

class Tenant extends Core
{
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'owner';

    protected $guarded = [];


    public function configure(): static
    {
        config([
            'database.connections.mysql.database' => $this->database,
        ]);

        DB::purge('mysql');

        app('cache')->purge(config('cache.default'));

        return $this;
    }


    public function use(): static
    {
        app()->forgetInstance('mysql');

        app()->instance('mysql', $this);

        return $this;
    }
}
