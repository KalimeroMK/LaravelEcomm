<?php

declare(strict_types=1);

namespace Modules\Shipping\Actions;

use Modules\Shipping\DTOs\ShippingDTO;
use Modules\Shipping\Models\Shipping;

class FindShippingAction
{
    public function execute(int $id): ShippingDTO
    {
        $shipping = Shipping::findOrFail($id);

        return new ShippingDTO($shipping);
    }
}
