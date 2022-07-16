<?php

namespace Modules\Cart\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Product\Models\Product;

class ProductStockRule implements Rule
{
    
    public function __construct()
    {
    }
    
    public function passes($attribute, $value): bool
    {
        if (Product::whereSlug(request()->slug)->first()->stock < request()['quant'][1]) {
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