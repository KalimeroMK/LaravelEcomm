<?php

declare(strict_types=1);

namespace Modules\Permission\Actions;

use Modules\Permission\DTOs\PermissionListDTO;
use Modules\Permission\Models\Permission;

class GetAllPermissionsAction
{
    public function execute(): PermissionListDTO
    {
        $permissions = Permission::all();

        return new PermissionListDTO($permissions);
    }
}
