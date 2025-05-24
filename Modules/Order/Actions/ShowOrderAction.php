<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Modules\Order\DTOs\OrderDTO;
use Modules\Order\Repository\OrderRepository;

readonly class ShowOrderAction
{
    public function __construct(private OrderRepository $repository)
    {
    }

    public function execute(int $id): OrderDTO
    {
        $order = $this->repository->findById($id);

        return OrderDTO::fromArray($order->toArray());
    }
}
