<?php

declare(strict_types=1);

namespace Modules\Shipping\Actions;

use Modules\Shipping\DTOs\ShippingListDTO;
use Modules\Shipping\Models\Shipping;

class GetAllShippingsAction
{
    public function execute(): ShippingListDTO
    {
        $shippings = Shipping::all();

        return new ShippingListDTO($shippings);
    }
}
