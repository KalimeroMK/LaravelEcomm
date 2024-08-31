<?php

namespace Modules\Permission\Service;

use Modules\Core\Service\CoreService;
use Modules\Permission\Repository\PermissionRepository;

class PermissionService extends CoreService
{
    public PermissionRepository $permission_repository;

    public function __construct(PermissionRepository $permissionRepository)
    {
        parent::__construct($permissionRepository);
        $this->permission_repository = $permissionRepository;
    }

}