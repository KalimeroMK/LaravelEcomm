<?php

declare(strict_types=1);

namespace Modules\Order\DTOs;

class OrderListDTO
{
    public $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }
}
