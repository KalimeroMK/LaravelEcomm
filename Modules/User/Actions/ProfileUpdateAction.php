<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Modules\User\DTOs\UserDTO;
use Modules\User\Models\User;

readonly class ProfileUpdateAction
{
    public function execute(User $user, UserDTO $dto): bool
    {
        return $user->fill([
            'name' => $dto->name,
            'email' => $dto->email,
            'status' => $dto->status,
        ])->save();
    }
}
