<?php

declare(strict_types=1);

namespace Modules\Shipping\Actions;

use Modules\Shipping\Models\Shipping;

class DeleteShippingAction
{
    public function execute(int $id): void
    {
        $shipping = Shipping::findOrFail($id);
        $shipping->delete();
    }
}
