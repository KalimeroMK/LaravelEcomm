<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Modules\User\DTOs\UserDTO;
use Modules\User\Models\User;
use Modules\User\Repository\UserRepository;

readonly class StoreUserAction
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function execute(UserDTO $dto): User
    {
        return $this->repository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'status' => $dto->status,
            'email_verified_at' => $dto->email_verified_at,
            'password' => bcrypt('password'), // или замени со $dto->password ако го додаваш во DTO
        ]);
    }
}
