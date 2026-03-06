<?php

declare(strict_types=1);

namespace Modules\Coupon\Tests\Unit\Actions;

use InvalidArgumentException;
use Modules\Cart\Models\Cart;
use Modules\Coupon\Actions\ApplyCouponAction;
use Modules\Coupon\Actions\CalculateDiscountAction;
use Modules\Coupon\Actions\ValidateCouponAction;
use Modules\Coupon\Models\Coupon;
use Modules\Coupon\Tests\CouponTestCase;
use Modules\Product\Models\Product;

class ApplyCouponActionTest extends CouponTestCase
{
    private ApplyCouponAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new ApplyCouponAction(
            new ValidateCouponAction(),
            new CalculateDiscountAction()
        );
    }

    public function test_applies_coupon_successfully(): void
    {
        $coupon = $this->createPercentCoupon(10);
        $user = $this->createUser();
        $product = Product::factory()->create(['price' => 100]);
        
        // Add item to cart
        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => $product->price,
            'quantity' => 1,
        ]);

        $result = $this->action->execute('PERCENT10', $user->id);

        $this->assertTrue($result['success']);
        $this->assertEquals('PERCENT10', $result['coupon']['code']);
        $this->assertEquals(10.00, $result['discount']);
        $this->assertEquals(100.00, $result['cart_subtotal']);
    }

    public function test_throws_exception_for_empty_cart(): void
    {
        $this->createPercentCoupon(10);
        $user = $this->createUser();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__('coupon.cart_empty'));

        $this->action->execute('PERCENT10', $user->id);
    }

    public function test_records_coupon_usage(): void
    {
        $coupon = $this->createPercentCoupon(10);
        $user = $this->createUser();
        $product = Product::factory()->create(['price' => 100]);
        
        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => $product->price,
            'quantity' => 1,
        ]);

        // First apply the coupon
        $this->action->execute('PERCENT10', $user->id);

        // Then record usage
        $orderId = 999; // Mock order ID
        $usage = $this->action->recordUsage($coupon->id, $orderId, $user->id, null, 10.00);

        $this->assertDatabaseHas('coupon_usage', [
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'order_id' => $orderId,
            'discount_amount' => 10.00,
        ]);

        $this->assertEquals(1, $coupon->fresh()->times_used);
    }

    public function test_removes_coupon_from_session(): void
    {
        $user = $this->createUser();
        
        // Set a coupon in session
        session()->put('coupon', ['code' => 'TEST']);

        $result = $this->action->remove();

        $this->assertTrue($result['success']);
        $this->assertNull(session()->get('coupon'));
    }

    public function test_returns_false_when_no_coupon_to_remove(): void
    {
        $result = $this->action->remove();

        $this->assertFalse($result['success']);
    }

    public function test_gets_applied_coupon(): void
    {
        $couponData = ['code' => 'TEST', 'discount' => 10];
        session()->put('coupon', $couponData);

        $result = $this->action->getAppliedCoupon();

        $this->assertEquals($couponData, $result);
    }

    public function test_checks_if_coupon_applied(): void
    {
        $this->assertFalse($this->action->hasCoupon());

        session()->put('coupon', ['code' => 'TEST']);

        $this->assertTrue($this->action->hasCoupon());
    }

    public function test_gets_discount_summary_with_free_shipping(): void
    {
        session()->put('coupon', [
            'code' => 'FREESHIP',
            'discount' => 0,
            'free_shipping' => true,
            'is_stackable' => false,
        ]);

        $summary = $this->action->getDiscountSummary(15.00);

        $this->assertTrue($summary['has_coupon']);
        $this->assertEquals(15.00, $summary['shipping_discount']);
        $this->assertEquals(15.00, $summary['total_discount']);
    }

    public function test_handles_free_shipping_coupon(): void
    {
        $coupon = $this->createFreeShippingCoupon();
        $user = $this->createUser();
        $product = Product::factory()->create(['price' => 100]);
        
        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => $product->price,
            'quantity' => 1,
        ]);

        $result = $this->action->execute('FREESHIP', $user->id);

        $this->assertTrue($result['success']);
        $this->assertTrue($result['coupon']['free_shipping']);
        $this->assertEquals(0, $result['discount']);
    }

    public function test_applies_minimum_amount_check(): void
    {
        $coupon = $this->createPercentCoupon(10, [
            'minimum_amount' => 200,
        ]);
        $user = $this->createUser();
        $product = Product::factory()->create(['price' => 50]);
        
        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => $product->price,
            'quantity' => 1,
        ]);

        $this->expectException(InvalidArgumentException::class);

        $this->action->execute('PERCENT10', $user->id);
    }
}
