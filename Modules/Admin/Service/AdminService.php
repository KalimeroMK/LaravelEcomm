<?php

namespace Modules\Admin\Service;

use Modules\Admin\Repository\AdminRepository;

class AdminService
{
    private AdminRepository $admin_repository;
    
    public function __construct(AdminRepository $admin_repository)
    {
        $this->admin_repository = $admin_repository;
    }
    
    public function index(): array
    {
        return $this->admin_repository->index();
    }
    
}