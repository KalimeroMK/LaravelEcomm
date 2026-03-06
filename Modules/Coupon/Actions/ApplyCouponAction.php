<?php

declare(strict_types=1);

namespace Modules\Coupon\Actions;

use InvalidArgumentException;
use Modules\Cart\Models\Cart;
use Modules\Coupon\Models\Coupon;
use Modules\Coupon\Models\CouponUsage;

readonly class ApplyCouponAction
{
    public function __construct(
        private ValidateCouponAction $validateCouponAction,
        private CalculateDiscountAction $calculateDiscountAction,
    ) {}

    /**
     * Apply coupon to cart
     *
     * @throws InvalidArgumentException
     */
    public function execute(
        string $code,
        int $userId,
        ?string $sessionId = null,
        ?int $customerGroupId = null
    ): array {
        // Get cart subtotal
        $cartQuery = Cart::where('user_id', $userId)->whereNull('order_id');
        $cartItems = $cartQuery->with('product')->get();

        if ($cartItems->isEmpty()) {
            throw new InvalidArgumentException(__('coupon.cart_empty'));
        }

        $cartSubtotal = $cartItems->sum(fn ($item) => $item->price * $item->quantity);

        // Validate coupon
        $coupon = $this->validateCouponAction->execute(
            $code,
            $userId,
            $sessionId,
            $customerGroupId,
            $cartSubtotal,
            $this->formatCartItems($cartItems)
        );

        // Calculate discount
        $discount = $this->calculateDiscountAction->execute(
            $coupon,
            $cartSubtotal,
            0, // Shipping cost will be calculated at checkout
            $this->formatCartItems($cartItems)
        );

        if ($discount <= 0 && !$coupon->isFreeShipping()) {
            throw new InvalidArgumentException(__('coupon.no_discount_applicable'));
        }

        // Build coupon data for session
        $couponData = [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'name' => $coupon->name,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'discount' => $discount,
            'free_shipping' => $coupon->isFreeShipping(),
            'is_stackable' => $coupon->is_stackable,
            'applies_to_shipping' => $coupon->isFreeShipping(),
        ];

        // Store in session
        session()->put('coupon', $couponData);

        return [
            'success' => true,
            'coupon' => $couponData,
            'cart_subtotal' => $cartSubtotal,
            'discount' => $discount,
            'message' => __('coupon.applied_successfully', ['code' => $coupon->code]),
        ];
    }

    /**
     * Remove coupon from cart
     */
    public function remove(): array
    {
        $coupon = session()->get('coupon');

        if (!$coupon) {
            return [
                'success' => false,
                'message' => __('coupon.no_coupon_applied'),
            ];
        }

        session()->forget('coupon');

        return [
            'success' => true,
            'message' => __('coupon.removed_successfully'),
        ];
    }

    /**
     * Get currently applied coupon
     */
    public function getAppliedCoupon(): ?array
    {
        return session()->get('coupon');
    }

    /**
     * Check if coupon is applied
     */
    public function hasCoupon(): bool
    {
        return session()->has('coupon');
    }

    /**
     * Record coupon usage after order is placed
     */
    public function recordUsage(
        int $couponId,
        int $orderId,
        ?int $userId,
        ?string $sessionId,
        float $discountAmount
    ): CouponUsage {
        // Record usage
        $usage = CouponUsage::recordUsage(
            $couponId,
            $userId,
            $orderId,
            $sessionId,
            $discountAmount
        );

        // Increment coupon usage counter
        $coupon = Coupon::find($couponId);
        if ($coupon) {
            $coupon->incrementUsage();
        }

        // Clear coupon from session
        session()->forget('coupon');

        return $usage;
    }

    /**
     * Format cart items for validation
     */
    private function formatCartItems($cartItems): array
    {
        return $cartItems->map(fn ($item) => [
            'product_id' => $item->product_id,
            'category_id' => $item->product?->category_id,
            'brand_id' => $item->product?->brand_id,
            'price' => $item->price,
            'quantity' => $item->quantity,
        ])->toArray();
    }

    /**
     * Get discount summary for display
     */
    public function getDiscountSummary(?float $shippingCost = 0): array
    {
        $coupon = $this->getAppliedCoupon();

        if (!$coupon) {
            return [
                'has_coupon' => false,
                'discount' => 0,
                'shipping_discount' => 0,
            ];
        }

        $shippingDiscount = ($coupon['free_shipping'] ?? false) ? $shippingCost : 0;

        return [
            'has_coupon' => true,
            'code' => $coupon['code'],
            'name' => $coupon['name'],
            'type' => $coupon['type'],
            'discount' => $coupon['discount'] ?? 0,
            'shipping_discount' => $shippingDiscount,
            'total_discount' => ($coupon['discount'] ?? 0) + $shippingDiscount,
            'is_stackable' => $coupon['is_stackable'] ?? false,
        ];
    }
}
