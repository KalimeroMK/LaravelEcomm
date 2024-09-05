<?php

namespace Modules\Role\Repository;

use Modules\Core\Repositories\Repository;
use Modules\Role\Models\Role;

class RoleRepository extends Repository
{
    public $model = Role::class;
}
