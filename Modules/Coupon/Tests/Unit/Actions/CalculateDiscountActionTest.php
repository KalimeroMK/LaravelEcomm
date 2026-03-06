<?php

declare(strict_types=1);

namespace Modules\Coupon\Tests\Unit\Actions;

use Modules\Coupon\Actions\CalculateDiscountAction;
use Modules\Coupon\Models\Coupon;
use Modules\Coupon\Tests\CouponTestCase;

class CalculateDiscountActionTest extends CouponTestCase
{
    private CalculateDiscountAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CalculateDiscountAction();
    }

    public function test_calculates_fixed_discount(): void
    {
        $coupon = $this->createFixedCoupon(10);

        $discount = $this->action->execute($coupon, 100);

        $this->assertEquals(10.00, $discount);
    }

    public function test_calculates_percentage_discount(): void
    {
        $coupon = $this->createPercentCoupon(20);

        $discount = $this->action->execute($coupon, 100);

        $this->assertEquals(20.00, $discount);
    }

    public function test_calculates_free_shipping_discount(): void
    {
        $coupon = $this->createFreeShippingCoupon();

        $discount = $this->action->execute($coupon, 100, 15.00);

        $this->assertEquals(15.00, $discount);
    }

    public function test_fixed_discount_cannot_exceed_subtotal(): void
    {
        $coupon = $this->createFixedCoupon(150);

        $discount = $this->action->execute($coupon, 100);

        $this->assertEquals(100.00, $discount);
    }

    public function test_applies_maximum_discount_cap(): void
    {
        $coupon = $this->createPercentCoupon(50, [
            'maximum_discount' => 30,
        ]);

        $discount = $this->action->execute($coupon, 200);

        $this->assertEquals(30.00, $discount);
    }

    public function test_returns_zero_when_minimum_amount_not_met(): void
    {
        $coupon = $this->createPercentCoupon(10, [
            'minimum_amount' => 100,
        ]);

        $discount = $this->action->execute($coupon, 50);

        $this->assertEquals(0.00, $discount);
    }

    public function test_calculates_discount_with_product_restrictions(): void
    {
        $coupon = $this->createPercentCoupon(10, [
            'applicable_products' => [1, 2],
        ]);

        $cartItems = [
            ['product_id' => 1, 'price' => 50, 'quantity' => 1],
            ['product_id' => 2, 'price' => 50, 'quantity' => 1],
            ['product_id' => 99, 'price' => 100, 'quantity' => 1], // Not applicable
        ];

        $discount = $this->action->execute($coupon, 200, 0, $cartItems);

        // 10% of 100 (only applicable products)
        $this->assertEquals(10.00, $discount);
    }

    public function test_calculates_final_total(): void
    {
        $total = $this->action->calculateFinalTotal(100, 20, 10, 5);

        // 100 - 20 + 10 + 5 = 95
        $this->assertEquals(95.00, $total);
    }

    public function test_final_total_cannot_be_negative(): void
    {
        $total = $this->action->calculateFinalTotal(50, 100, 0, 0);

        $this->assertEquals(0.00, $total);
    }

    public function test_calculates_discount_breakdown_for_cart_items(): void
    {
        $coupon = $this->createPercentCoupon(10);

        $cartItems = [
            ['product_id' => 1, 'price' => 50, 'quantity' => 2], // 100 total
            ['product_id' => 2, 'price' => 100, 'quantity' => 1], // 100 total
        ];

        $result = $this->action->executeForCartItems($coupon, $cartItems);

        $this->assertEquals(20.00, $result['total']); // 10% of 200
        $this->assertEquals(200.00, $result['discountable_subtotal']);
        $this->assertCount(2, $result['items']);
    }

    public function test_discount_proportionally_distributed(): void
    {
        $coupon = $this->createPercentCoupon(10);

        $cartItems = [
            ['product_id' => 1, 'price' => 50, 'quantity' => 1], // 50 total - should get 5
            ['product_id' => 2, 'price' => 150, 'quantity' => 1], // 150 total - should get 15
        ];

        $result = $this->action->executeForCartItems($coupon, $cartItems);

        $this->assertEquals(20.00, $result['total']);
        
        // Check proportional distribution
        $item1Discount = collect($result['items'])->firstWhere('product_id', 1)['discount'];
        $item2Discount = collect($result['items'])->firstWhere('product_id', 2)['discount'];
        
        $this->assertEquals(5.00, $item1Discount); // 25% of discount
        $this->assertEquals(15.00, $item2Discount); // 75% of discount
    }
}
