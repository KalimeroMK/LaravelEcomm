<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Illuminate\Support\Facades\Hash;
use Modules\User\DTOs\UserDTO;
use Modules\User\Repository\UserRepository;

readonly class RegisterUserAction
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function execute(UserDTO $dto): array
    {
        $user = $this->repository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'status' => $dto->status,
            'email_verified_at' => $dto->email_verified_at,
            'password' => Hash::make($dto->password ?? 'password'),
        ]);

        return [
            'token' => $user->createToken('MyAuthApp')->plainTextToken,
            'name' => $user->name,
        ];
    }
}
