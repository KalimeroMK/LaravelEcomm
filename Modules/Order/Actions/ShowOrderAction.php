<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Modules\Order\DTOs\OrderDTO;
use Modules\Order\Models\Order;

class ShowOrderAction
{
    public function execute(int $id): OrderDTO
    {
        $order = Order::findOrFail($id);

        return new OrderDTO($order);
    }
}
