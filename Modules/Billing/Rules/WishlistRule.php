<?php

declare(strict_types=1);

namespace Modules\Billing\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Billing\Models\Wishlist;
use Modules\Product\Models\Product;

class WishlistRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        $product = Product::whereSlug($value)->first();
        if (empty($product)) {
            return false;
        }

        return empty(Wishlist::whereProductId($product->id)->whereUserId(Auth()->id())->first());
    }

    public function message(): string
    {
        return 'Product is already add to  wishlist or  slug is invalid.';
    }
}
