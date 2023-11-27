<?php

namespace Modules\Cart\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Product\Models\Product;

class ProductStockRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        $productSlug = request()->input('slug');
        $product = Product::whereSlug($productSlug)->first();

        // Check if product exists and if the stock is sufficient
        if ($product && is_array($value)) {
            foreach ($value as $qty) {
                if ($product->stock < $qty) {
                    return false;
                }
            }
        }

        return true;
    }

    public function message(): string
    {
        return 'Out of stock, You can add other products';
    }
}
