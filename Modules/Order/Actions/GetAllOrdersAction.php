<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Order\Repository\OrderRepository;

readonly class GetAllOrdersAction
{
    public function __construct(private OrderRepository $repository) {}

    public function execute(): LengthAwarePaginator
    {
        // Paginated with user/carts/shipping eager-loaded — Order::all() plus a
        // per-row user/shipping lookup does not scale on the admin index.
        return $this->repository->paginateAll();
    }
}
