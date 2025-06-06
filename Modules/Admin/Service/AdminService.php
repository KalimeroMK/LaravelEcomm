<?php

declare(strict_types=1);

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

    /**
     * @return array<string, int> Array of paths.
     */
    public function index(): array
    {
        return $this->admin_repository->usersLastSevenDays();
    }

    /**
     * @return Collection<int, \Modules\Order\Models\Order>
     */
    public function OrdersByMonth(): Collection
    {
        return $this->admin_repository->getPaidOrdersCountByMonth();
    }
}
