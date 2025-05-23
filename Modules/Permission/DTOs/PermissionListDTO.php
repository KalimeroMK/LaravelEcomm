<?php

declare(strict_types=1);

namespace Modules\Permission\DTOs;

class PermissionListDTO
{
    public array $permissions;

    public function __construct($permissions)
    {
        $this->permissions = $permissions->toArray();
    }
}
