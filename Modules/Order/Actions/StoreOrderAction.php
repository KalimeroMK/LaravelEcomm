<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Modules\Order\DTOs\OrderDTO;
use Modules\Order\Repository\OrderRepository;

readonly class StoreOrderAction
{
    public function __construct(private OrderRepository $repository)
    {
    }

    public function execute(array $data): OrderDTO
    {
        $order = $this->repository->create($data);

        return OrderDTO::fromArray($order->toArray());
    }
}
