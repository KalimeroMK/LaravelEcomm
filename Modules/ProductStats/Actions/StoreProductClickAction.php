<?php

declare(strict_types=1);

namespace Modules\ProductStats\Actions;

use Modules\ProductStats\Events\ProductClicked;
use Modules\ProductStats\Models\ProductClick;

readonly class StoreProductClickAction
{
    public function execute(int $productId, ?int $userId, string $ipAddress): ProductClick
    {
        $click = ProductClick::create([
            'product_id' => $productId,
            'user_id' => $userId,
            'ip_address' => $ipAddress,
        ]);

        event(new ProductClicked($click));

        return $click;
    }
}
