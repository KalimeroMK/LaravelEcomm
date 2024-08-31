<?php

namespace Modules\Permission\Repository;

use Modules\Core\Repositories\Repository;
use Modules\Permission\Models\Permission;

class PermissionRepository extends Repository
{
    public $model = Permission::class;

}