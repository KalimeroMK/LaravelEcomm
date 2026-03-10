<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Coupon;

use InvalidArgumentException;
use Modules\Coupon\Actions\ValidateCouponAction;
use Modules\Coupon\Models\Coupon;
use Modules\Coupon\Models\CouponUsage;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class ValidateCouponActionTest extends ActionTestCase
{
    public function test_execute_validates_active_coupon(): void
    {
        // Arrange
        $coupon = Coupon::factory()->create([
            'code' => 'VALID10',
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'status' => Coupon::STATUS_ACTIVE,
            'starts_at' => null,
            'expires_at' => null,
        ]);

        $action = new ValidateCouponAction();

        // Act
        $result = $action->execute('VALID10', 1);

        // Assert
        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertEquals($coupon->id, $result->id);
    }

    public function test_execute_throws_exception_for_invalid_code(): void
    {
        // Arrange
        $action = new ValidateCouponAction();

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__('coupon.invalid_code'));
        $action->execute('INVALIDCODE', 1);
    }

    public function test_execute_throws_exception_for_inactive_coupon(): void
    {
        // Arrange
        Coupon::factory()->create([
            'code' => 'INACTIVE',
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'status' => Coupon::STATUS_INACTIVE,
        ]);

        $action = new ValidateCouponAction();

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__('coupon.expired_or_inactive'));
        $action->execute('INACTIVE', 1);
    }

    public function test_execute_throws_exception_for_expired_coupon(): void
    {
        // Arrange
        Coupon::factory()->create([
            'code' => 'EXPIRED',
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'status' => Coupon::STATUS_ACTIVE,
            'expires_at' => now()->subDay(),
        ]);

        $action = new ValidateCouponAction();

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__('coupon.expired_or_inactive'));
        $action->execute('EXPIRED', 1);
    }

    public function test_execute_throws_exception_for_future_coupon(): void
    {
        // Arrange
        Coupon::factory()->create([
            'code' => 'FUTURE',
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'status' => Coupon::STATUS_ACTIVE,
            'starts_at' => now()->addDay(),
        ]);

        $action = new ValidateCouponAction();

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__('coupon.expired_or_inactive'));
        $action->execute('FUTURE', 1);
    }

    public function test_execute_throws_exception_when_usage_limit_reached(): void
    {
        // Arrange
        Coupon::factory()->create([
            'code' => 'LIMITED',
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'status' => Coupon::STATUS_ACTIVE,
            'usage_limit' => 5,
            'times_used' => 5,
        ]);

        $action = new ValidateCouponAction();

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__('coupon.usage_limit_reached'));
        $action->execute('LIMITED', 1);
    }

    public function test_execute_throws_exception_when_user_limit_reached(): void
    {
        // Arrange
        $user = User::factory()->create();
        $coupon = Coupon::factory()->create([
            'code' => 'PERUSER',
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'status' => Coupon::STATUS_ACTIVE,
            'usage_limit_per_user' => 1,
        ]);

        // Record usage for this user using the model directly
        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'order_id' => \Modules\Order\Models\Order::factory()->create()->id,
            'discount_amount' => 10.00,
            'used_at' => now(),
        ]);

        $action = new ValidateCouponAction();

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__('coupon.usage_limit_per_user_reached'));
        $action->execute('PERUSER', $user->id);
    }

    public function test_execute_throws_exception_when_not_applicable_to_customer(): void
    {
        // Arrange
        $user = User::factory()->create();
        Coupon::factory()->create([
            'code' => 'SPECIFIC',
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'status' => Coupon::STATUS_ACTIVE,
            'customer_ids' => [999], // Different user
        ]);

        $action = new ValidateCouponAction();

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__('coupon.not_applicable_to_customer'));
        $action->execute('SPECIFIC', $user->id);
    }

    public function test_execute_throws_exception_when_minimum_amount_not_met(): void
    {
        // Arrange
        Coupon::factory()->create([
            'code' => 'MIN100',
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'status' => Coupon::STATUS_ACTIVE,
            'minimum_amount' => 100.00,
        ]);

        $action = new ValidateCouponAction();

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $action->execute('MIN100', 1, null, null, 50.00);
    }

    public function test_execute_throws_exception_when_no_applicable_products(): void
    {
        // Arrange
        Coupon::factory()->create([
            'code' => 'PRODUCT',
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'status' => Coupon::STATUS_ACTIVE,
            'applicable_products' => [999],
        ]);

        $action = new ValidateCouponAction();

        $cartItems = [
            ['product_id' => 1, 'category_id' => 1, 'brand_id' => 1, 'price' => 50.00, 'quantity' => 1],
        ];

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__('coupon.no_applicable_products'));
        $action->execute('PRODUCT', 1, null, null, 50.00, $cartItems);
    }

    public function test_is_valid_returns_true_for_valid_coupon(): void
    {
        // Arrange
        Coupon::factory()->create([
            'code' => 'VALIDCHECK',
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'status' => Coupon::STATUS_ACTIVE,
        ]);

        $action = new ValidateCouponAction();

        // Act
        $result = $action->isValid('VALIDCHECK', 1);

        // Assert
        $this->assertTrue($result);
    }

    public function test_is_valid_returns_false_for_invalid_coupon(): void
    {
        // Arrange
        $action = new ValidateCouponAction();

        // Act
        $result = $action->isValid('INVALIDCHECK', 1);

        // Assert
        $this->assertFalse($result);
    }

    public function test_execute_validates_with_session_id(): void
    {
        // Arrange
        $coupon = Coupon::factory()->create([
            'code' => 'SESSION',
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'status' => Coupon::STATUS_ACTIVE,
        ]);

        $action = new ValidateCouponAction();

        // Act
        $result = $action->execute('SESSION', null, 'test-session-123');

        // Assert
        $this->assertInstanceOf(Coupon::class, $result);
    }
}
