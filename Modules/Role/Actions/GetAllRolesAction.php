<?php

declare(strict_types=1);

namespace Modules\Role\Actions;

use Modules\Role\DTOs\RoleListDTO;
use Modules\Role\Models\Role;

class GetAllRolesAction
{
    public function execute(): RoleListDTO
    {
        $roles = Role::with('permissions')->get();

        return new RoleListDTO($roles);
    }
}
