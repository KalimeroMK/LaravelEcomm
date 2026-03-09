<?php

declare(strict_types=1);

namespace Modules\Cart\Actions;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\Cart\Models\Cart;

readonly class GetUserCartAction
{
    /**
     * @return Collection<int, Cart>
     */
    public function execute(): Collection
    {
        return Cart::with(['product', 'product.media'])
            ->whereUserId(Auth::id())
            ->whereOrderId(null)
            ->get();
    }
}
