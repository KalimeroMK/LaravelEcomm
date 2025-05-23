<?php

declare(strict_types=1);

namespace Modules\User\DTOs;

class UserDTO
{
    public array $user;

    public function __construct($user)
    {
        $this->user = $user->toArray();
    }
}
