<?php

declare(strict_types=1);

namespace Modules\Post\Actions;

use Illuminate\Support\Collection;
use Modules\User\Models\User;
use Modules\User\Repository\UserRepository;

readonly class GetAllUsersAction
{
    public function __construct(private UserRepository $repository) {}

    public function execute(): Collection
    {
        return $this->repository->findAll();
    }
}
