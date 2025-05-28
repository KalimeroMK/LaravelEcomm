<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Illuminate\Support\Facades\Hash;
use Modules\User\DTOs\UserDTO;
use Modules\User\Models\User;
use Modules\User\Repository\UserRepository;

readonly class RegisterUserAction
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function execute(UserDTO $dto): User
    {
        return $this->repository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'email_verified_at' => $dto->email_verified_at,
            'password' => Hash::make($dto->password ?? 'password'),
        ]);
    }
}
