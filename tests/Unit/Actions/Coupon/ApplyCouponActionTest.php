<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Coupon;

use InvalidArgumentException;
use Modules\Cart\Models\Cart;
use Modules\Coupon\Actions\ApplyCouponAction;
use Modules\Coupon\Database\Factories\CouponFactory;
use Modules\Product\Database\Factories\ProductFactory;
use Modules\User\Database\Factories\UserFactory;
use Tests\Unit\Actions\ActionTestCase;

class ApplyCouponActionTest extends ActionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        session()->start();
    }

    public function testExecuteThrowsExceptionForEmptyCart(): void
    {
        $user = UserFactory::new()->create();
        $coupon = CouponFactory::new()->create();

        $action = app(ApplyCouponAction::class);
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Your cart is empty');
        
        $action->execute($coupon->code, $user->id);
    }

    public function testExecuteAppliesValidCoupon(): void
    {
        $user = UserFactory::new()->create();
        $product = ProductFactory::new()->create(['price' => 100]);
        $coupon = CouponFactory::new()->percentage()->create(['value' => 10]); // 10% off
        
        Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => 100,
            'quantity' => 1,
            'order_id' => null,
        ]);

        $action = app(ApplyCouponAction::class);
        $result = $action->execute($coupon->code, $user->id);

        $this->assertTrue($result['success']);
        $this->assertEquals($coupon->code, $result['coupon']['code']);
        $this->assertArrayHasKey('discount', $result['coupon']);
    }

    public function testRemoveCouponClearsSession(): void
    {
        session()->put('coupon', ['code' => 'TEST10', 'discount' => 10]);
        
        $action = app(ApplyCouponAction::class);
        $result = $action->remove();

        $this->assertTrue($result['success']);
        $this->assertFalse(session()->has('coupon'));
    }

    public function testGetAppliedCouponReturnsNullWhenNoCoupon(): void
    {
        $action = app(ApplyCouponAction::class);
        $result = $action->getAppliedCoupon();

        $this->assertNull($result);
    }

    public function testHasCouponReturnsFalseWhenNoCoupon(): void
    {
        $action = app(ApplyCouponAction::class);
        $result = $action->hasCoupon();

        $this->assertFalse($result);
    }

    public function testHasCouponReturnsTrueWhenCouponExists(): void
    {
        session()->put('coupon', ['code' => 'TEST10']);
        
        $action = app(ApplyCouponAction::class);
        $result = $action->hasCoupon();

        $this->assertTrue($result);
    }
}
