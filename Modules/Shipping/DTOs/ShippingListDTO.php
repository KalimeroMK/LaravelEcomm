<?php

declare(strict_types=1);

namespace Modules\Shipping\DTOs;

class ShippingListDTO
{
    public array $shippings;

    public function __construct($shippings)
    {
        $this->shippings = $shippings->toArray();
    }
}
