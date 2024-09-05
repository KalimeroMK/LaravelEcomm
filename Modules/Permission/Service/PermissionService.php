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

    /**
     * Get all products based on given data.
     *
     * @param  array<string, mixed>  $data
     */
    public function search(array $data): mixed
    {
        return $this->permission_repository->search($data);
    }
}
