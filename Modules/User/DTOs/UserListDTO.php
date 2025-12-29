<?php

declare(strict_types=1);

namespace Modules\User\DTOs;

use Illuminate\Support\Collection;

class UserListDTO
{
    public Collection $users;

    public function __construct($users)
    {
        $this->users = $users instanceof Collection ? $users : collect($users);
    }
}
