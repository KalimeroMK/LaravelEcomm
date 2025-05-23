<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Modules\User\Models\User;

class StoreUserAction
{
    public function execute(array $data): User
    {
        return User::create($data);
    }
}
