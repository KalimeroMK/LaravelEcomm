<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Modules\Order\DTOs\OrderListDTO;
use Modules\Order\Repository\OrderRepository;

readonly class FindOrdersByUserAction
{
    public function __construct(private OrderRepository $repository) {}

    public function execute(int $userId): OrderListDTO
    {
        $orders = $this->repository->findAllByUser($userId);

        return new OrderListDTO($orders);
    }
}
