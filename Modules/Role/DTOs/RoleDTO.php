<?php

declare(strict_types=1);

namespace Modules\Role\DTOs;

use Modules\Role\Models\Role;

class RoleDTO
{
    public int $id;

    public string $name;

    public array $permissions;

    public function __construct(Role $role)
    {
        $this->id = $role->id;
        $this->name = $role->name;
        $this->permissions = $role->permissions ? $role->permissions->toArray() : [];
    }
}
