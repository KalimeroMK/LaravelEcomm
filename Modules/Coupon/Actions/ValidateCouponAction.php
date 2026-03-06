<?php

declare(strict_types=1);

namespace Modules\Coupon\Actions;

use InvalidArgumentException;
use Modules\Cart\Models\Cart;
use Modules\Coupon\Models\Coupon;
use Modules\Product\Models\Product;

readonly class ValidateCouponAction
{
    /**
     * Validate if coupon can be applied to cart
     *
     * @throws InvalidArgumentException
     */
    public function execute(
        string $code,
        ?int $userId,
        ?string $sessionId = null,
        ?int $customerGroupId = null,
        ?float $cartSubtotal = null,
        ?array $cartItems = null
    ): Coupon {
        $coupon = Coupon::byCode($code)->first();

        if (!$coupon) {
            throw new InvalidArgumentException(__('coupon.invalid_code'));
        }

        // Check if coupon is valid (active and in date range)
        if (!$coupon->isValid()) {
            throw new InvalidArgumentException(__('coupon.expired_or_inactive'));
        }

        // Check global usage limit
        if ($coupon->isUsageLimitReached()) {
            throw new InvalidArgumentException(__('coupon.usage_limit_reached'));
        }

        // Check per-user usage limit
        if ($coupon->isUsageLimitReachedForUser($userId, $sessionId)) {
            throw new InvalidArgumentException(__('coupon.usage_limit_per_user_reached'));
        }

        // Check customer applicability
        if (!$coupon->isApplicableToCustomer($userId, $customerGroupId)) {
            throw new InvalidArgumentException(__('coupon.not_applicable_to_customer'));
        }

        // If cart data provided, validate cart-level restrictions
        if ($cartSubtotal !== null) {
            $this->validateCartRestrictions($coupon, $cartSubtotal);
        }

        // If cart items provided, validate product restrictions
        if ($cartItems !== null && !empty($cartItems)) {
            $this->validateProductRestrictions($coupon, $cartItems);
        }

        return $coupon;
    }

    /**
     * Validate cart-level restrictions
     *
     * @throws InvalidArgumentException
     */
    private function validateCartRestrictions(Coupon $coupon, float $cartSubtotal): void
    {
        // Check minimum amount
        if ($coupon->minimum_amount !== null && $cartSubtotal < $coupon->minimum_amount) {
            throw new InvalidArgumentException(
                __('coupon.minimum_amount_not_met', [
                    'amount' => number_format($coupon->minimum_amount, 2),
                ])
            );
        }
    }

    /**
     * Validate product restrictions
     *
     * @param array $cartItems Array of cart items with product data
     * @throws InvalidArgumentException
     */
    private function validateProductRestrictions(Coupon $coupon, array $cartItems): void
    {
        $applicableItems = [];
        $hasRestrictions = $this->hasProductRestrictions($coupon);

        foreach ($cartItems as $item) {
            $productId = $item['product_id'] ?? null;
            $categoryId = $item['category_id'] ?? null;
            $brandId = $item['brand_id'] ?? null;

            if ($productId && $coupon->isApplicableToProduct($productId, $categoryId, $brandId)) {
                $applicableItems[] = $item;
            }
        }

        // If coupon has product restrictions and no items match
        if ($hasRestrictions && empty($applicableItems)) {
            throw new InvalidArgumentException(__('coupon.no_applicable_products'));
        }
    }

    /**
     * Check if coupon has product/category/brand restrictions
     */
    private function hasProductRestrictions(Coupon $coupon): bool
    {
        return $coupon->applicable_products !== null
            || $coupon->applicable_categories !== null
            || $coupon->applicable_brands !== null
            || $coupon->excluded_products !== null
            || $coupon->excluded_categories !== null
            || $coupon->excluded_brands !== null;
    }

    /**
     * Quick validation without throwing exceptions
     */
    public function isValid(
        string $code,
        ?int $userId,
        ?string $sessionId = null,
        ?int $customerGroupId = null,
        ?float $cartSubtotal = null
    ): bool {
        try {
            $this->execute($code, $userId, $sessionId, $customerGroupId, $cartSubtotal);
            return true;
        } catch (InvalidArgumentException) {
            return false;
        }
    }
}
