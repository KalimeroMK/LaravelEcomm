<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Modules\User\Models\User;

class UpdateUserAction
{
    public function execute(int $id, array $data): void
    {
        $user = User::findOrFail($id);
        $user->update($data);
    }
}
