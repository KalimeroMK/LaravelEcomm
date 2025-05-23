<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Spatie\Permission\Models\Role;

class GetAllRolesAction
{
    public function execute(): array
    {
        return Role::all()->toArray();
    }
}
