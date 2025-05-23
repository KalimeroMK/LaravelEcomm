<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Modules\Order\DTOs\OrderListDTO;
use Modules\Order\Models\Order;

class FindOrdersByUserAction
{
    public function execute($userId): OrderListDTO
    {
        $orders = Order::where('user_id', $userId)->get();

        return new OrderListDTO($orders);
    }
}
