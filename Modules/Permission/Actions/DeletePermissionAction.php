<?php

declare(strict_types=1);

namespace Modules\Permission\Actions;

use Modules\Permission\Models\Permission;

class DeletePermissionAction
{
    public function execute(int $id): void
    {
        Permission::destroy($id);
    }
}
