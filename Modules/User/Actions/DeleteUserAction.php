<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Modules\User\Models\User;

class DeleteUserAction
{
    public function execute(int $id): void
    {
        $user = User::findOrFail($id);
        $user->delete();
    }
}
