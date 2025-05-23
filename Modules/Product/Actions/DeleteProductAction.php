<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Modules\Product\Models\Product;

class DeleteProductAction
{
    public function execute(int $id): void
    {
        Product::destroy($id);
    }
}
