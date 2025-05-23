<?php

declare(strict_types=1);

namespace Modules\Shipping\DTOs;

class ShippingDTO
{
    public array $shipping;

    public function __construct($shipping)
    {
        $this->shipping = $shipping->toArray();
    }
}
