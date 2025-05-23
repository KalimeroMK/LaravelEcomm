<?php

declare(strict_types=1);

namespace Modules\Permission\Actions;

use Modules\Permission\DTOs\PermissionDTO;
use Modules\Permission\Models\Permission;

class UpdatePermissionAction
{
    public function execute(int $id, array $data): PermissionDTO
    {
        $permission = Permission::findOrFail($id);
        $permission->update($data);

        return new PermissionDTO($permission);
    }
}
