<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Coupon;

use InvalidArgumentException;
use Modules\Cart\Models\Cart;
use Modules\Coupon\Actions\ApplyCouponAction;
use Modules\Coupon\Actions\CalculateDiscountAction;
use Modules\Coupon\Actions\ValidateCouponAction;
use Modules\Coupon\Models\Coupon;
use Modules\Coupon\Models\CouponUsage;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class ApplyCouponActionTest extends ActionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Clear session before each test
        session()->forget('coupon');
    }

    public function test_execute_applies_valid_fixed_coupon(): void
    {
        // Arrange
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100.00]);
        Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => 100.00,
            'quantity' => 1,
        ]);

        $coupon = Coupon::factory()->create([
            'code' => 'SAVE10',
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'status' => Coupon::STATUS_ACTIVE,
            'minimum_amount' => null,
        ]);

        $validateAction = new ValidateCouponAction();
        $calculateAction = new CalculateDiscountAction();
        $action = new ApplyCouponAction($validateAction, $calculateAction);

        // Act
        $result = $action->execute('SAVE10', $user->id);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals('SAVE10', $result['coupon']['code']);
        $this->assertEquals(10.00, $result['discount']);
        $this->assertEquals(100.00, $result['cart_subtotal']);
    }

    public function test_execute_applies_valid_percent_coupon(): void
    {
        // Arrange
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100.00]);
        Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => 100.00,
            'quantity' => 2,
        ]);

        $coupon = Coupon::factory()->create([
            'code' => 'SAVE20PCT',
            'type' => Coupon::TYPE_PERCENT,
            'value' => 20.00,
            'status' => Coupon::STATUS_ACTIVE,
        ]);

        $validateAction = new ValidateCouponAction();
        $calculateAction = new CalculateDiscountAction();
        $action = new ApplyCouponAction($validateAction, $calculateAction);

        // Act
        $result = $action->execute('SAVE20PCT', $user->id);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals('SAVE20PCT', $result['coupon']['code']);
        $this->assertEquals(40.00, $result['discount']);
    }

    public function test_execute_throws_exception_when_cart_is_empty(): void
    {
        // Arrange
        $user = User::factory()->create();

        $coupon = Coupon::factory()->create([
            'code' => 'SAVE10',
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'status' => Coupon::STATUS_ACTIVE,
        ]);

        $validateAction = new ValidateCouponAction();
        $calculateAction = new CalculateDiscountAction();
        $action = new ApplyCouponAction($validateAction, $calculateAction);

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__('coupon.cart_empty'));
        $action->execute('SAVE10', $user->id);
    }

    public function test_remove_clears_coupon_from_session(): void
    {
        // Arrange
        session()->put('coupon', ['code' => 'SAVE10', 'discount' => 10.00]);

        $validateAction = new ValidateCouponAction();
        $calculateAction = new CalculateDiscountAction();
        $action = new ApplyCouponAction($validateAction, $calculateAction);

        // Act
        $result = $action->remove();

        // Assert
        $this->assertTrue($result['success']);
        $this->assertFalse(session()->has('coupon'));
    }

    public function test_remove_returns_error_when_no_coupon_applied(): void
    {
        // Arrange
        $validateAction = new ValidateCouponAction();
        $calculateAction = new CalculateDiscountAction();
        $action = new ApplyCouponAction($validateAction, $calculateAction);

        // Act
        $result = $action->remove();

        // Assert
        $this->assertFalse($result['success']);
        $this->assertEquals(__('coupon.no_coupon_applied'), $result['message']);
    }

    public function test_get_applied_coupon_returns_coupon_data(): void
    {
        // Arrange
        $couponData = ['code' => 'SAVE10', 'discount' => 10.00];
        session()->put('coupon', $couponData);

        $validateAction = new ValidateCouponAction();
        $calculateAction = new CalculateDiscountAction();
        $action = new ApplyCouponAction($validateAction, $calculateAction);

        // Act
        $result = $action->getAppliedCoupon();

        // Assert
        $this->assertEquals($couponData, $result);
    }

    public function test_get_applied_coupon_returns_null_when_no_coupon(): void
    {
        // Arrange
        $validateAction = new ValidateCouponAction();
        $calculateAction = new CalculateDiscountAction();
        $action = new ApplyCouponAction($validateAction, $calculateAction);

        // Act
        $result = $action->getAppliedCoupon();

        // Assert
        $this->assertNull($result);
    }

    public function test_has_coupon_returns_true_when_coupon_exists(): void
    {
        // Arrange
        session()->put('coupon', ['code' => 'SAVE10']);

        $validateAction = new ValidateCouponAction();
        $calculateAction = new CalculateDiscountAction();
        $action = new ApplyCouponAction($validateAction, $calculateAction);

        // Act & Assert
        $this->assertTrue($action->hasCoupon());
    }

    public function test_has_coupon_returns_false_when_no_coupon(): void
    {
        // Arrange
        $validateAction = new ValidateCouponAction();
        $calculateAction = new CalculateDiscountAction();
        $action = new ApplyCouponAction($validateAction, $calculateAction);

        // Act & Assert
        $this->assertFalse($action->hasCoupon());
    }

    public function test_get_discount_summary_returns_data_when_coupon_applied(): void
    {
        // Arrange
        session()->put('coupon', [
            'code' => 'SAVE10',
            'name' => 'Save $10',
            'type' => Coupon::TYPE_FIXED,
            'discount' => 10.00,
            'free_shipping' => true,
            'is_stackable' => false,
        ]);

        $validateAction = new ValidateCouponAction();
        $calculateAction = new CalculateDiscountAction();
        $action = new ApplyCouponAction($validateAction, $calculateAction);

        // Act
        $result = $action->getDiscountSummary(5.00);

        // Assert
        $this->assertTrue($result['has_coupon']);
        $this->assertEquals('SAVE10', $result['code']);
        $this->assertEquals(10.00, $result['discount']);
        $this->assertEquals(5.00, $result['shipping_discount']);
        $this->assertEquals(15.00, $result['total_discount']);
    }

    public function test_get_discount_summary_returns_no_discount_when_no_coupon(): void
    {
        // Arrange
        $validateAction = new ValidateCouponAction();
        $calculateAction = new CalculateDiscountAction();
        $action = new ApplyCouponAction($validateAction, $calculateAction);

        // Act
        $result = $action->getDiscountSummary(5.00);

        // Assert
        $this->assertFalse($result['has_coupon']);
        $this->assertEquals(0, $result['discount']);
        $this->assertEquals(0, $result['shipping_discount']);
    }

    public function test_record_usage_creates_usage_record(): void
    {
        // Arrange
        $user = User::factory()->create();
        $coupon = Coupon::factory()->create([
            'code' => 'SAVE10',
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'times_used' => 0,
        ]);

        $order = \Modules\Order\Models\Order::factory()->create([
            'user_id' => $user->id,
        ]);

        session()->put('coupon', ['id' => $coupon->id, 'code' => 'SAVE10']);

        $validateAction = new ValidateCouponAction();
        $calculateAction = new CalculateDiscountAction();
        $action = new ApplyCouponAction($validateAction, $calculateAction);

        // Act
        $usage = $action->recordUsage($coupon->id, $order->id, $user->id, null, 10.00);

        // Assert
        $this->assertDatabaseHas('coupon_usage', [
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'order_id' => $order->id,
            'discount_amount' => 10.00,
        ]);

        // Refresh coupon and check times_used was incremented
        $coupon->refresh();
        $this->assertEquals(1, $coupon->times_used);

        // Session should be cleared
        $this->assertFalse(session()->has('coupon'));
    }
}
