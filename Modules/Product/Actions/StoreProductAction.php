<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Modules\Product\DTOs\ProductDTO;
use Modules\Product\Models\Product;

class StoreProductAction
{
    public function execute(array $data): ProductDTO
    {
        $product = Product::create($data);
        // Attach relationships if needed (categories, tags, attributes)
        if (isset($data['category'])) {
            $product->categories()->sync($data['category']);
        }
        if (isset($data['tag'])) {
            $product->tags()->sync($data['tag']);
        }

        // ... handle attributes if needed
        return new ProductDTO($product->fresh(['categories', 'tags', 'brand', 'attributes.attribute', 'author']));
    }
}
