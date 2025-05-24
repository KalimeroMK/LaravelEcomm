<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Modules\Order\DTOs\OrderListDTO;
use Modules\Order\Repository\OrderRepository;

readonly class GetAllOrdersAction
{
    public function __construct(private OrderRepository $repository)
    {
    }

    public function execute(): OrderListDTO
    {
        $orders = $this->repository->findAll();

        return new OrderListDTO($orders);
    }
}
