<?php

declare(strict_types=1);

namespace Modules\Permission\Actions;

use Modules\Permission\DTOs\PermissionDTO;
use Modules\Permission\Models\Permission;

class CreatePermissionAction
{
    public function execute(array $data): PermissionDTO
    {
        $permission = Permission::create($data);

        return new PermissionDTO($permission);
    }
}
