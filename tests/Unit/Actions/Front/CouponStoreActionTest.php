<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use InvalidArgumentException;
use Modules\Cart\Models\Cart;
use Modules\Coupon\Models\Coupon;
use Modules\Front\Actions\CouponStoreAction;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class CouponStoreActionTest extends ActionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        session()->forget('coupon');
    }

    public function test_execute_throws_exception_for_invalid_code(): void
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $action = new CouponStoreAction();

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid coupon code');
        $action->execute('INVALID');
    }

    public function test_execute_throws_exception_for_inactive_coupon(): void
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create coupon directly to avoid factory issues
        $coupon = new Coupon([
            'code' => 'INACTIVE',
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'status' => Coupon::STATUS_INACTIVE,
        ]);
        $coupon->save();

        $action = new CouponStoreAction();

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Coupon is not active');
        $action->execute('INACTIVE');
    }

    public function test_execute_throws_exception_for_expired_coupon(): void
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create coupon directly to avoid factory issues
        $coupon = new Coupon([
            'code' => 'EXPIRED',
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'status' => Coupon::STATUS_ACTIVE,
            'expires_at' => now()->subDay(),
        ]);
        $coupon->save();

        $action = new CouponStoreAction();

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Coupon has expired');
        $action->execute('EXPIRED');
    }

    public function test_execute_applies_valid_fixed_coupon(): void
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create(['price' => 100.00]);
        Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => 100.00,
            'quantity' => 1,
            'order_id' => null,
        ]);

        // Create coupon directly
        $coupon = new Coupon([
            'code' => 'SAVE20',
            'type' => Coupon::TYPE_FIXED,
            'value' => 20.00,
            'status' => Coupon::STATUS_ACTIVE,
        ]);
        $coupon->save();

        $action = new CouponStoreAction();

        // Act
        $result = $action->execute('SAVE20');

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals($coupon->id, $result['id']);
        $this->assertEquals('SAVE20', $result['code']);
        $this->assertEquals(20.00, $result['value']);
    }

    public function test_execute_applies_valid_percent_coupon(): void
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create(['price' => 100.00]);
        Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => 100.00,
            'quantity' => 2,
            'order_id' => null,
        ]);

        // Create coupon directly
        $coupon = new Coupon([
            'code' => 'SAVE50PCT',
            'type' => Coupon::TYPE_PERCENT,
            'value' => 50.00,
            'status' => Coupon::STATUS_ACTIVE,
        ]);
        $coupon->save();

        $action = new CouponStoreAction();

        // Act
        $result = $action->execute('SAVE50PCT');

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals('SAVE50PCT', $result['code']);
        $this->assertGreaterThan(0, $result['value']); // Should have some discount value
    }

    public function test_execute_stores_coupon_in_session(): void
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create(['price' => 100.00]);
        Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => 100.00,
            'order_id' => null,
        ]);

        // Create coupon directly
        $coupon = new Coupon([
            'code' => 'SESSION',
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'status' => Coupon::STATUS_ACTIVE,
        ]);
        $coupon->save();

        $action = new CouponStoreAction();

        // Act
        $action->execute('SESSION');

        // Assert
        $this->assertTrue(session()->has('coupon'));
        $this->assertEquals('SESSION', session('coupon.code'));
    }

    public function test_execute_calculates_discount_based_on_cart_total(): void
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create(['price' => 200.00]);
        Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => 200.00,
            'order_id' => null,
        ]);

        // Create coupon directly
        $coupon = new Coupon([
            'code' => 'PERCENT10',
            'type' => Coupon::TYPE_PERCENT,
            'value' => 10.00,
            'status' => Coupon::STATUS_ACTIVE,
        ]);
        $coupon->save();

        $action = new CouponStoreAction();

        // Act
        $result = $action->execute('PERCENT10');

        // Assert
        $this->assertEquals(20.00, $result['value']); // 10% of 200
    }

    public function test_execute_applies_free_shipping_coupon(): void
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create(['price' => 100.00]);
        Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => 100.00,
            'order_id' => null,
        ]);

        // Create coupon with free_shipping flag (TYPE_FREE_SHIPPING is not in DB enum)
        $coupon = new Coupon([
            'code' => 'FREESHIP',
            'type' => Coupon::TYPE_FIXED,
            'value' => 0.00,
            'status' => Coupon::STATUS_ACTIVE,
            'free_shipping' => true,
        ]);
        $coupon->save();

        $action = new CouponStoreAction();

        // Act
        $result = $action->execute('FREESHIP');

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals('FREESHIP', $result['code']);
    }
}
