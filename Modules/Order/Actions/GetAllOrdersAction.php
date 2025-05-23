<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Modules\Order\DTOs\OrderListDTO;
use Modules\Order\Models\Order;

class GetAllOrdersAction
{
    public function execute(): OrderListDTO
    {
        $orders = Order::all();

        return new OrderListDTO($orders);
    }
}
