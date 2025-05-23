<?php

declare(strict_types=1);

namespace Modules\Permission\DTOs;

use Modules\Permission\Models\Permission;

class PermissionDTO
{
    public int $id;

    public string $name;

    public string $guard_name;

    public string $created_at;

    public function __construct(Permission $permission)
    {
        $this->id = $permission->id;
        $this->name = $permission->name;
        $this->guard_name = $permission->guard_name;
        $this->created_at = $permission->created_at->toDateTimeString();
    }
}
