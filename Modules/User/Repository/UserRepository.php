<?php

declare(strict_types=1);

namespace Modules\User\Repository;

use Illuminate\Support\Collection;
use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Repositories\EloquentRepository;
use Modules\User\Models\User;

class UserRepository extends EloquentRepository implements EloquentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(User::class);
    }

    /**
     * Get all users with roles, ordered by ID descending.
     */
    public function findAll(): Collection
    {
        return (new $this->modelClass)->with('roles')->orderBy('id', 'desc')->get();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->modelClass::where('email', $email)->first();
    }
}
