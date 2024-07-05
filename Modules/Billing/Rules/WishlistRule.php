<?php

namespace Modules\Billing\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Billing\Models\Wishlist;
use Modules\Product\Models\Product;

class WishlistRule implements Rule
{
    public function __construct() {}

    public function passes($attribute, $value): bool
    {
        $product = Product::where('slug', $value)->first();
        if (empty($product)) {
            return false;
        }

        if (! empty(Wishlist::whereProductId($product->id)->whereUserId(Auth()->id())->first())) {
            return false;
        }

        return true;
    }

    public function message(): string
    {
        return 'Product is already add to  wishlist or  slug is invalid.';
    }
}
