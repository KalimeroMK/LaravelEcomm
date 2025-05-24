<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Modules\Order\DTOs\OrderDTO;
use Modules\Order\Repository\OrderRepository;

readonly class UpdateOrderAction
{
    public function __construct(private OrderRepository $repository)
    {
    }

    public function execute(int $id, array $data): OrderDTO
    {
        $order = $this->repository->update($id, $data);

        return OrderDTO::fromArray($order->toArray());
    }
}
