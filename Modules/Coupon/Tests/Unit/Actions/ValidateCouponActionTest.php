<?php

declare(strict_types=1);

namespace Modules\Coupon\Tests\Unit\Actions;

use InvalidArgumentException;
use Modules\Coupon\Actions\ValidateCouponAction;
use Modules\Coupon\Models\Coupon;
use Modules\Coupon\Tests\CouponTestCase;

class ValidateCouponActionTest extends CouponTestCase
{
    private ValidateCouponAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new ValidateCouponAction();
    }

    public function test_validates_active_coupon(): void
    {
        $coupon = $this->createCoupon();
        $user = $this->createUser();

        $result = $this->action->execute('TEST10', $user->id);

        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertEquals($coupon->id, $result->id);
    }

    public function test_throws_exception_for_invalid_code(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__('coupon.invalid_code'));

        $this->action->execute('INVALID', 1);
    }

    public function test_throws_exception_for_inactive_coupon(): void
    {
        $this->createCoupon(['status' => Coupon::STATUS_INACTIVE]);
        $user = $this->createUser();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__('coupon.expired_or_inactive'));

        $this->action->execute('TEST10', $user->id);
    }

    public function test_throws_exception_for_expired_coupon(): void
    {
        $this->createCoupon(['expires_at' => now()->subDay()]);
        $user = $this->createUser();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__('coupon.expired_or_inactive'));

        $this->action->execute('TEST10', $user->id);
    }

    public function test_throws_exception_for_future_coupon(): void
    {
        $this->createCoupon(['starts_at' => now()->addDay()]);
        $user = $this->createUser();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__('coupon.expired_or_inactive'));

        $this->action->execute('TEST10', $user->id);
    }

    public function test_throws_exception_when_usage_limit_reached(): void
    {
        $this->createCoupon([
            'usage_limit' => 5,
            'times_used' => 5,
        ]);
        $user = $this->createUser();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__('coupon.usage_limit_reached'));

        $this->action->execute('TEST10', $user->id);
    }

    public function test_throws_exception_when_user_limit_reached(): void
    {
        $coupon = $this->createCoupon([
            'usage_limit_per_user' => 1,
        ]);
        $user = $this->createUser();

        // Record one usage
        \Modules\Coupon\Models\CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'discount_amount' => 10,
            'used_at' => now(),
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__('coupon.usage_limit_per_user_reached'));

        $this->action->execute('TEST10', $user->id);
    }

    public function test_throws_exception_for_minimum_amount_not_met(): void
    {
        $this->createCoupon([
            'minimum_amount' => 100,
        ]);
        $user = $this->createUser();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__('coupon.minimum_amount_not_met', ['amount' => '100.00']));

        $this->action->execute('TEST10', $user->id, null, null, 50);
    }

    public function test_validates_coupon_with_sufficient_amount(): void
    {
        $coupon = $this->createCoupon([
            'minimum_amount' => 100,
        ]);
        $user = $this->createUser();

        $result = $this->action->execute('TEST10', $user->id, null, null, 150);

        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertEquals($coupon->id, $result->id);
    }

    public function test_validates_product_restrictions(): void
    {
        $coupon = $this->createCoupon([
            'applicable_products' => [1, 2, 3],
        ]);
        $user = $this->createUser();

        // Cart with applicable product
        $cartItems = [
            ['product_id' => 1, 'category_id' => 1, 'brand_id' => 1, 'price' => 50, 'quantity' => 1],
        ];

        $result = $this->action->execute('TEST10', $user->id, null, null, 50, $cartItems);

        $this->assertInstanceOf(Coupon::class, $result);
    }

    public function test_throws_exception_when_no_applicable_products(): void
    {
        $this->createCoupon([
            'applicable_products' => [1, 2, 3],
        ]);
        $user = $this->createUser();

        // Cart with non-applicable product
        $cartItems = [
            ['product_id' => 99, 'category_id' => 1, 'brand_id' => 1, 'price' => 50, 'quantity' => 1],
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__('coupon.no_applicable_products'));

        $this->action->execute('TEST10', $user->id, null, null, 50, $cartItems);
    }

    public function test_quick_validation_returns_true_for_valid_coupon(): void
    {
        $this->createCoupon();
        $user = $this->createUser();

        $isValid = $this->action->isValid('TEST10', $user->id);

        $this->assertTrue($isValid);
    }

    public function test_quick_validation_returns_false_for_invalid_coupon(): void
    {
        $isValid = $this->action->isValid('INVALID', 1);

        $this->assertFalse($isValid);
    }
}
