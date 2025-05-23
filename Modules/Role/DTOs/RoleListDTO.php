<?php

declare(strict_types=1);

namespace Modules\Role\DTOs;

class RoleListDTO
{
    public array $roles;

    public function __construct($roles)
    {
        $this->roles = $roles->toArray();
    }
}
