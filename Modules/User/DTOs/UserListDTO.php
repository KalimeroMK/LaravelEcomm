<?php

declare(strict_types=1);

namespace Modules\User\DTOs;

class UserListDTO
{
    public array $users;

    public function __construct($users)
    {
        $this->users = $users->toArray();
    }
}
