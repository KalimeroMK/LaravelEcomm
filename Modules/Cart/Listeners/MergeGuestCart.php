<?php

declare(strict_types=1);

namespace Modules\Cart\Listeners;

use Illuminate\Auth\Events\Login;
use Modules\Cart\Models\Cart;

/**
 * When a guest logs in, claim the cart rows they built anonymously
 * (keyed by session id) and merge them into their account cart.
 */
class MergeGuestCart
{
    public function handle(Login $event): void
    {
        $userId = $event->user->getAuthIdentifier();
        $sessionId = session()->getId();

        $guestRows = Cart::whereNull('user_id')
            ->whereNull('order_id')
            ->where('session_id', $sessionId)
            ->get();

        foreach ($guestRows as $row) {
            $existing = Cart::where('user_id', $userId)
                ->where('product_id', $row->product_id)
                ->whereNull('order_id')
                ->first();

            if ($existing instanceof Cart) {
                $existing->quantity += $row->quantity;
                $existing->amount = $existing->price * $existing->quantity;
                $existing->save();
                $row->delete();

                continue;
            }

            $row->user_id = $userId;
            $row->save();
        }
    }
}
