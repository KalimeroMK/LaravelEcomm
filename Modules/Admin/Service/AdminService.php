<?php

namespace Modules\Admin\Service;

use Illuminate\Database\Eloquent\Collection;
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
        return $this->admin_repository->usersLastSevenDays();
    }

    public function OrdersByMonth(): Collection
    {
        return $this->admin_repository->getPaidOrdersCountByMonth();
    }

}