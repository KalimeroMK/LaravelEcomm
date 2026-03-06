<?php

declare(strict_types=1);

namespace Modules\Coupon\Actions;

use Modules\Coupon\Models\Coupon;

readonly class CalculateDiscountAction
{
    /**
     * Calculate discount amount for a coupon
     *
     * @param Coupon $coupon
     * @param float $subtotal Cart subtotal
     * @param float|null $shippingCost Shipping cost (for free shipping coupons)
     * @param array|null $cartItems Cart items with product details for product-specific discounts
     * @return float Calculated discount amount
     */
    public function execute(
        Coupon $coupon,
        float $subtotal,
        ?float $shippingCost = 0,
        ?array $cartItems = null
    ): float {
        // Check minimum amount requirement
        if ($coupon->minimum_amount !== null && $subtotal < $coupon->minimum_amount) {
            return 0;
        }

        $discountableAmount = $this->getDiscountableAmount($coupon, $subtotal, $cartItems);

        $discount = match ($coupon->type) {
            Coupon::TYPE_FREE_SHIPPING => $shippingCost ?? 0,
            Coupon::TYPE_FIXED => min($coupon->value, $discountableAmount),
            Coupon::TYPE_PERCENT => $discountableAmount * ($coupon->value / 100),
            default => 0,
        };

        // Apply maximum discount cap for percentage coupons
        if ($coupon->maximum_discount !== null && $discount > $coupon->maximum_discount) {
            $discount = $coupon->maximum_discount;
        }

        // Ensure discount doesn't exceed subtotal (except for free shipping)
        if ($coupon->type !== Coupon::TYPE_FREE_SHIPPING && $discount > $discountableAmount) {
            $discount = $discountableAmount;
        }

        return round($discount, 2);
    }

    /**
     * Calculate discount for cart with multiple items
     * Returns breakdown per item
     *
     * @param Coupon $coupon
     * @param array $cartItems Array of items with 'product_id', 'category_id', 'brand_id', 'price', 'quantity'
     * @return array ['total' => float, 'items' => array]
     */
    public function executeForCartItems(Coupon $coupon, array $cartItems): array
    {
        $discountableSubtotal = 0;
        $applicableItems = [];

        // Calculate discountable subtotal based on product restrictions
        foreach ($cartItems as $item) {
            $productId = $item['product_id'] ?? null;
            $categoryId = $item['category_id'] ?? null;
            $brandId = $item['brand_id'] ?? null;
            $price = $item['price'] ?? 0;
            $quantity = $item['quantity'] ?? 1;

            if ($coupon->isApplicableToProduct($productId, $categoryId, $brandId)) {
                $itemTotal = $price * $quantity;
                $discountableSubtotal += $itemTotal;
                $applicableItems[] = [
                    ...$item,
                    'total' => $itemTotal,
                ];
            }
        }

        // Calculate total discount
        $totalDiscount = $this->execute($coupon, $discountableSubtotal, 0, $cartItems);

        // Distribute discount proportionally to applicable items
        $itemDiscounts = [];
        if ($discountableSubtotal > 0) {
            foreach ($applicableItems as $item) {
                $proportion = $item['total'] / $discountableSubtotal;
                $itemDiscount = round($totalDiscount * $proportion, 2);
                $itemDiscounts[] = [
                    'product_id' => $item['product_id'],
                    'discount' => $itemDiscount,
                ];
            }
        }

        return [
            'total' => $totalDiscount,
            'discountable_subtotal' => $discountableSubtotal,
            'items' => $itemDiscounts,
        ];
    }

    /**
     * Get the amount that can be discounted
     */
    private function getDiscountableAmount(Coupon $coupon, float $subtotal, ?array $cartItems): float
    {
        // If no product restrictions or no cart items, discount applies to whole subtotal
        if (!$this->hasProductRestrictions($coupon) || $cartItems === null) {
            return $subtotal;
        }

        // Calculate only applicable items subtotal
        $discountableAmount = 0;
        foreach ($cartItems as $item) {
            $productId = $item['product_id'] ?? null;
            $categoryId = $item['category_id'] ?? null;
            $brandId = $item['brand_id'] ?? null;
            $price = $item['price'] ?? 0;
            $quantity = $item['quantity'] ?? 1;

            if ($coupon->isApplicableToProduct($productId, $categoryId, $brandId)) {
                $discountableAmount += ($price * $quantity);
            }
        }

        return $discountableAmount;
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
     * Calculate final total after discount
     */
    public function calculateFinalTotal(
        float $subtotal,
        float $discount,
        float $shippingCost = 0,
        float $tax = 0
    ): float {
        return max(0, $subtotal - $discount + $shippingCost + $tax);
    }
}
