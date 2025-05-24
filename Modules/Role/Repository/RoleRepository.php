<?php

declare(strict_types=1);

namespace Modules\Role\Repository;

use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Repositories\EloquentRepository;
use Modules\Role\Models\Role;

class RoleRepository extends EloquentRepository implements EloquentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(Role::class);
    }
}
