<?php

namespace Modules\Billing\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Cart\Models\Cart;

class CartExistRule implements Rule
{
    public function __construct()
    {
    }
    
    public function passes($attribute, $value): bool
    {
        if (empty(Cart::whereUserId(auth()->user()->id)->whereOrderId(null)->first())) {
            return false;
        }
        
        return true;
    }
    
    public function message(): string
    {
        return 'Cart is Empty !';
    }
}