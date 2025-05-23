<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Modules\User\Models\User;

class GetUserRolesAction
{
    public function execute(int $userId): array
    {
        $user = User::findOrFail($userId);

        return $user->roles->pluck('name', 'name')->all();
    }
}
