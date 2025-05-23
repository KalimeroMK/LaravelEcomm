<?php

declare(strict_types=1);

namespace Modules\Order\DTOs;

use Modules\Order\Models\Order;

class OrderDTO
{
    public int $id;

    public int $user_id;

    public float $total;

    public string $created_at;

    public function __construct(Order $order)
    {
        $this->id = $order->id;
        $this->user_id = $order->user_id;
        $this->total = $order->total;
        $this->created_at = $order->created_at->toDateTimeString();
    }
}
