<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Modules\User\DTOs\UserDTO;
use Modules\User\Models\User;

class FindUserAction
{
    public function execute(int $id): UserDTO
    {
        $user = User::findOrFail($id);

        return new UserDTO($user);
    }
}
