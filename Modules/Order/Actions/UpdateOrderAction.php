<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Modules\Order\DTOs\OrderDTO;
use Modules\Order\Models\Order;

class UpdateOrderAction
{
    public function execute(int $id, array $data): OrderDTO
    {
        $order = Order::findOrFail($id);
        $order->update($data);

        return new OrderDTO($order);
    }
}
