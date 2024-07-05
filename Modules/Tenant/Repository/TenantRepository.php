<?php

namespace Modules\Tenant\Repository;

use Modules\Core\Repositories\Repository;
use Modules\Tenant\Models\Tenant;

class TenantRepository extends Repository
{
    /**
     * @var string
     */
    public $model = Tenant::class;
}
