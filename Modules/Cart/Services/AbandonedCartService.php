<?php

declare(strict_types=1);

namespace Modules\Cart\Services;

use Illuminate\Support\Facades\Log;
use Modules\Cart\Jobs\SendAbandonedCartFirstEmailJob;
use Modules\Cart\Jobs\SendAbandonedCartSecondEmailJob;
use Modules\Cart\Jobs\SendAbandonedCartThirdEmailJob;
use Modules\Cart\Models\AbandonedCart;
use Modules\Cart\Models\Cart;
use Modules\User\Models\User;

class AbandonedCartService
{
    /**
     * Track abandoned cart when user adds items but doesn't complete purchase
     */
    public function trackAbandonedCart(?User $user = null, ?string $sessionId = null, ?string $email = null): void
    {
        try {
            // Get cart items for the user or session
            $cartItems = $this->getCartItems($user, $sessionId);

            if (empty($cartItems)) {
                return;
            }

            $totalAmount = collect($cartItems)->sum('amount');
            $totalItems = collect($cartItems)->sum('quantity');

            // Check if we already have an abandoned cart for this user/session
            $existingAbandonedCart = $this->findExistingAbandonedCart($user, $sessionId, $email);

            if ($existingAbandonedCart) {
                // Update existing abandoned cart
                $existingAbandonedCart->update([
                    'cart_data' => $cartItems,
                    'total_amount' => $totalAmount,
                    'total_items' => $totalItems,
                    'last_activity' => now(),
                    'email' => $email ?? $existingAbandonedCart->email,
                ]);
            } else {
                // Create new abandoned cart
                $abandonedCart = AbandonedCart::create([
                    'user_id' => $user?->id,
                    'session_id' => $sessionId,
                    'email' => $email ?? $user?->email,
                    'cart_data' => $cartItems,
                    'total_amount' => $totalAmount,
                    'total_items' => $totalItems,
                    'last_activity' => now(),
                ]);

                // Schedule first email (1 hour after cart abandonment)
                SendAbandonedCartFirstEmailJob::dispatch($abandonedCart)->delay(now()->addHour());
            }
        } catch (\Exception $e) {
            Log::error('Failed to track abandoned cart: ' . $e->getMessage());
        }
    }

    /**
     * Get cart items for user or session
     */
    private function getCartItems(?User $user = null, ?string $sessionId = null): array
    {
        $query = Cart::with('product')
            ->where('status', 'new')
            ->whereNull('order_id');

        if ($user) {
            $query->where('user_id', $user->id);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        } else {
            return [];
        }

        return $query->get()->map(function ($cart) {
            return [
                'product_id' => $cart->product_id,
                'quantity' => $cart->quantity,
                'price' => $cart->price,
                'amount' => $cart->amount,
            ];
        })->toArray();
    }

    /**
     * Find existing abandoned cart
     */
    private function findExistingAbandonedCart(?User $user = null, ?string $sessionId = null, ?string $email = null): ?AbandonedCart
    {
        $query = AbandonedCart::where('converted', false);

        if ($user) {
            $query->where('user_id', $user->id);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        } elseif ($email) {
            $query->where('email', $email);
        } else {
            return null;
        }

        return $query->first();
    }

    /**
     * Process abandoned cart emails (called by scheduled command)
     */
    public function processAbandonedCartEmails(): void
    {
        // Send first emails (1 hour after abandonment)
        $firstEmailCarts = AbandonedCart::needsFirstEmail()->get();
        foreach ($firstEmailCarts as $cart) {
            SendAbandonedCartFirstEmailJob::dispatch($cart);
        }

        // Send second emails (24 hours after first email)
        $secondEmailCarts = AbandonedCart::needsSecondEmail()->get();
        foreach ($secondEmailCarts as $cart) {
            SendAbandonedCartSecondEmailJob::dispatch($cart);
        }

        // Send third emails (72 hours after second email)
        $thirdEmailCarts = AbandonedCart::needsThirdEmail()->get();
        foreach ($thirdEmailCarts as $cart) {
            SendAbandonedCartThirdEmailJob::dispatch($cart);
        }
    }

    /**
     * Mark abandoned cart as converted when user completes purchase
     */
    public function markAsConverted(?User $user = null, ?string $sessionId = null): void
    {
        $abandonedCart = $this->findExistingAbandonedCart($user, $sessionId);

        if ($abandonedCart) {
            $abandonedCart->markAsConverted();
        }
    }

    /**
     * Clean up old abandoned carts (older than 30 days)
     */
    public function cleanupOldAbandonedCarts(): void
    {
        AbandonedCart::where('created_at', '<', now()->subDays(30))
            ->where('converted', true)
            ->delete();
    }

    /**
     * Get abandoned cart statistics
     */
    public function getAbandonedCartStats(): array
    {
        $total = AbandonedCart::count();
        $converted = AbandonedCart::where('converted', true)->count();
        $conversionRate = $total > 0 ? ($converted / $total) * 100 : 0;

        return [
            'total_abandoned_carts' => $total,
            'converted_carts' => $converted,
            'conversion_rate' => round($conversionRate, 2),
            'pending_first_email' => AbandonedCart::needsFirstEmail()->count(),
            'pending_second_email' => AbandonedCart::needsSecondEmail()->count(),
            'pending_third_email' => AbandonedCart::needsThirdEmail()->count(),
        ];
    }
}
