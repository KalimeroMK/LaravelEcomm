<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Modules\Order\DTOs\OrderListDTO;
use Modules\Order\Repository\OrderRepository;

readonly class SearchOrdersAction
{
    public function __construct(private OrderRepository $repository)
    {
    }

    public function execute(array $criteria): OrderListDTO
    {
        $orders = $this->repository->search($criteria);

        return new OrderListDTO($orders);
    }
}
