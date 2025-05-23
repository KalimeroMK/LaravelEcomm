<?php

declare(strict_types=1);

namespace Modules\Role\Actions;

use Modules\Role\DTOs\RoleDTO;
use Modules\Role\Models\Role;

class UpdateRoleAction
{
    public function execute(int $id, array $data): RoleDTO
    {
        $role = Role::findOrFail($id);
        $role->update(['name' => $data['name']]);
        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return new RoleDTO($role->fresh('permissions'));
    }
}
