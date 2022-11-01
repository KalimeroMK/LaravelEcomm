<?php

namespace Modules\Cart\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;
use Modules\Product\Models\Product;

class ProductStockRule implements Rule
{
    
    public function passes($attribute, $value): bool
    {
        if (Product::whereSlug(Arr::get(request()->all(), 'slug'))->first()->stock < request()->quantity) {
            return false;
        } else {
            return true;
        }
    }
    
    public function message(): string
    {
        return 'Out of stock, You can add other products';
    }
    
}