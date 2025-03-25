<?php

declare(strict_types=1);

namespace Modules\Order\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Cart\Models\Cart;

class CartRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        if (empty(Cart::whereUserId(Auth()->id())->whereOrderId(null)->first())) {
            return false;
        }

        return true;

    }

    public function message(): string
    {
        return 'Cart is empty.';
    }
}
