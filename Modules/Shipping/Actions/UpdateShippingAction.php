<?php

declare(strict_types=1);

namespace Modules\Shipping\Actions;

use Modules\Shipping\Models\Shipping;

class UpdateShippingAction
{
    public function execute(int $id, array $data): void
    {
        $shipping = Shipping::findOrFail($id);
        $shipping->update($data);
    }
}
