<?php

declare(strict_types=1);

namespace Modules\Billing\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Cart\Models\Cart;

class CartExistRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        return ! empty(Cart::whereUserId(auth()->user()->id)->whereOrderId(null)->first());
    }

    public function message(): string
    {
        return 'Cart is Empty !';
    }
}
