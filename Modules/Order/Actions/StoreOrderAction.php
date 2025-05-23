<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Modules\Order\DTOs\OrderDTO;
use Modules\Order\Models\Order;

class StoreOrderAction
{
    public function execute(array $data): OrderDTO
    {
        $order = Order::create($data);

        return new OrderDTO($order);
    }
}
