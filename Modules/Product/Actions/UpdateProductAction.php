<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Modules\Product\DTOs\ProductDTO;
use Modules\Product\Models\Product;

class UpdateProductAction
{
    public function execute(int $id, array $data): ProductDTO
    {
        $product = Product::findOrFail($id);
        $product->update($data);
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
