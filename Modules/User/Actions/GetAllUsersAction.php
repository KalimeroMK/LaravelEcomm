<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Modules\User\DTOs\UserListDTO;
use Modules\User\Models\User;

class GetAllUsersAction
{
    public function execute(): UserListDTO
    {
        $users = User::all();

        return new UserListDTO($users);
    }
}
