<?php

namespace Modules\Product\Models\Observers;

use Illuminate\Support\Str;
use Modules\Product\Models\Product;

class ProductObserver
{
    /**
     * Handle the product "created" event.
     *
     * @param  Product  $product
     */
    public function creating(Product $product): void
    {
        $slug = Str::slug($product->title);
        if (Product::whereSlug($slug)->count() > 0) {
            $product->slug = $slug;
        }
        $product->slug = $product->incrementSlug($slug);
    }
}
