<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Modules\User\DTOs\UserDTO;
use Modules\User\Models\User;
use Modules\User\Repository\UserRepository;

readonly class StoreUserAction
{
    public function __construct(private UserRepository $repository) {}

    public function execute(UserDTO $dto): User
    {
        // Fail loudly rather than falling back to a shared default password.
        // A default here would make every admin-created account trivially
        // guessable.
        if ($dto->password === null || $dto->password === '') {
            throw new InvalidArgumentException('A password is required to create a user.');
        }

        return $this->repository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'email_verified_at' => $dto->email_verified_at,
            'password' => Hash::make($dto->password),
        ]);
    }
}
