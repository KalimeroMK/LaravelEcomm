<?php

namespace Modules\Role\Service;

use Modules\Core\Service\CoreService;
use Modules\Role\Repository\RoleRepository;

class RoleService extends CoreService
{
    public RoleRepository $role_repository;

    public function __construct(RoleRepository $roleRepository)
    {
        parent::__construct($roleRepository);
        $this->role_repository = $roleRepository;
    }
}
