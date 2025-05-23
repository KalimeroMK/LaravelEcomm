<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Modules\User\Models\User;

class ProfileUpdateAction
{
    public function execute(User $user, array $data): bool
    {
        return $user->fill($data)->save();
    }
}
