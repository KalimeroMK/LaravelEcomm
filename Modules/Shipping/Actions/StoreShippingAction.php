<?php

declare(strict_types=1);

namespace Modules\Shipping\Actions;

use Modules\Shipping\Models\Shipping;

class StoreShippingAction
{
    public function execute(array $data): Shipping
    {
        return Shipping::create($data);
    }
}
