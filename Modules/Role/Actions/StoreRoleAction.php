<?php

declare(strict_types=1);

namespace Modules\Role\Actions;

use Modules\Role\DTOs\RoleDTO;
use Modules\Role\Models\Role;

class StoreRoleAction
{
    public function execute(array $data): RoleDTO
    {
        $role = Role::create(['name' => $data['name']]);
        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return new RoleDTO($role->fresh('permissions'));
    }
}
