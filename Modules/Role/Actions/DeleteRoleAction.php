<?php

declare(strict_types=1);

namespace Modules\Role\Actions;

use Modules\Role\Models\Role;

class DeleteRoleAction
{
    public function execute(int $id): void
    {
        Role::destroy($id);
    }
}
