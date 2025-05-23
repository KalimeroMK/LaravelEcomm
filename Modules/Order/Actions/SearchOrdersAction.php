<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Modules\Order\DTOs\OrderListDTO;
use Modules\Order\Models\Order;

class SearchOrdersAction
{
    public function execute(array $criteria): OrderListDTO
    {
        $orders = Order::query();
        foreach ($criteria as $key => $value) {
            $orders->where($key, $value);
        }

        return new OrderListDTO($orders->get());
    }
}
