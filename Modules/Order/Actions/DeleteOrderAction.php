<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Modules\Order\Models\Order;

class DeleteOrderAction
{
    public function execute(int $id): void
    {
        Order::destroy($id);
    }
}
