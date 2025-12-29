<?php

declare(strict_types=1);

namespace Modules\Cart\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\Cart\Models\Cart;

readonly class GetUserCartAction
{
    public function execute(): Collection
    {
        return Cart::whereUserId(Auth::id())
            ->whereOrderId(null)
            ->get();
    }
}
