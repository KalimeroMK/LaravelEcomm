<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Modules\User\DTOs\UserDTO;
use Modules\User\Repository\UserRepository;

readonly class FindUserAction
{
    public function __construct(private UserRepository $repository) {}

    public function execute(int $id): UserDTO
    {
        $user = $this->repository->findById($id);

        return UserDTO::fromArray($user->toArray());
    }
}
