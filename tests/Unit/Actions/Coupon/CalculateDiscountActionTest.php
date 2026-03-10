<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Coupon;

use Modules\Coupon\Actions\CalculateDiscountAction;
use Modules\Coupon\Models\Coupon;
use Tests\Unit\Actions\ActionTestCase;

class CalculateDiscountActionTest extends ActionTestCase
{
    public function test_execute_calculates_fixed_discount(): void
    {
        // Arrange
        $coupon = Coupon::factory()->make([
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'minimum_amount' => null,
            'maximum_discount' => null,
        ]);

        $action = new CalculateDiscountAction();

        // Act
        $result = $action->execute($coupon, 100.00);

        // Assert
        $this->assertEquals(10.00, $result);
    }

    public function test_execute_calculates_percent_discount(): void
    {
        // Arrange
        $coupon = Coupon::factory()->make([
            'type' => Coupon::TYPE_PERCENT,
            'value' => 20.00,
            'minimum_amount' => null,
            'maximum_discount' => null,
        ]);

        $action = new CalculateDiscountAction();

        // Act
        $result = $action->execute($coupon, 100.00);

        // Assert
        $this->assertEquals(20.00, $result);
    }

    public function test_execute_calculates_free_shipping_discount(): void
    {
        // Arrange
        $coupon = Coupon::factory()->make([
            'type' => Coupon::TYPE_FREE_SHIPPING,
            'value' => 0,
        ]);

        $action = new CalculateDiscountAction();

        // Act
        $result = $action->execute($coupon, 100.00, 15.00);

        // Assert
        $this->assertEquals(15.00, $result);
    }

    public function test_execute_returns_zero_when_minimum_amount_not_met(): void
    {
        // Arrange
        $coupon = Coupon::factory()->make([
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'minimum_amount' => 50.00,
        ]);

        $action = new CalculateDiscountAction();

        // Act
        $result = $action->execute($coupon, 30.00);

        // Assert
        $this->assertEquals(0.00, $result);
    }

    public function test_execute_applies_discount_when_minimum_amount_met(): void
    {
        // Arrange
        $coupon = Coupon::factory()->make([
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'minimum_amount' => 50.00,
        ]);

        $action = new CalculateDiscountAction();

        // Act
        $result = $action->execute($coupon, 75.00);

        // Assert
        $this->assertEquals(10.00, $result);
    }

    public function test_execute_caps_discount_at_maximum_discount(): void
    {
        // Arrange
        $coupon = Coupon::factory()->make([
            'type' => Coupon::TYPE_PERCENT,
            'value' => 50.00,
            'maximum_discount' => 20.00,
        ]);

        $action = new CalculateDiscountAction();

        // Act
        $result = $action->execute($coupon, 100.00);

        // Assert
        $this->assertEquals(20.00, $result);
    }

    public function test_execute_discount_cannot_exceed_subtotal(): void
    {
        // Arrange
        $coupon = Coupon::factory()->make([
            'type' => Coupon::TYPE_FIXED,
            'value' => 150.00,
        ]);

        $action = new CalculateDiscountAction();

        // Act
        $result = $action->execute($coupon, 100.00);

        // Assert
        $this->assertEquals(100.00, $result);
    }

    public function test_execute_calculates_with_product_restrictions(): void
    {
        // Arrange
        $coupon = Coupon::factory()->make([
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'applicable_products' => [1],
        ]);

        $cartItems = [
            ['product_id' => 1, 'price' => 50.00, 'quantity' => 1],
            ['product_id' => 2, 'price' => 50.00, 'quantity' => 1],
        ];

        $action = new CalculateDiscountAction();

        // Act
        $result = $action->execute($coupon, 100.00, 0, $cartItems);

        // Assert - only applies to product 1 ($50)
        $this->assertEquals(10.00, $result);
    }

    public function test_execute_for_cart_items_returns_breakdown(): void
    {
        // Arrange
        $coupon = Coupon::factory()->make([
            'type' => Coupon::TYPE_PERCENT,
            'value' => 10.00,
        ]);

        $cartItems = [
            ['product_id' => 1, 'price' => 50.00, 'quantity' => 1],
            ['product_id' => 2, 'price' => 50.00, 'quantity' => 1],
        ];

        $action = new CalculateDiscountAction();

        // Act
        $result = $action->executeForCartItems($coupon, $cartItems);

        // Assert
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('discountable_subtotal', $result);
        $this->assertArrayHasKey('items', $result);
        $this->assertEquals(100.00, $result['discountable_subtotal']);
        $this->assertEquals(10.00, $result['total']);
        $this->assertCount(2, $result['items']);
    }

    public function test_calculate_final_total_returns_correct_value(): void
    {
        // Arrange
        $action = new CalculateDiscountAction();

        // Act
        $result = $action->calculateFinalTotal(100.00, 10.00, 5.00, 8.00);

        // Assert: 100 - 10 + 5 + 8 = 103
        $this->assertEquals(103.00, $result);
    }

    public function test_calculate_final_total_cannot_be_negative(): void
    {
        // Arrange
        $action = new CalculateDiscountAction();

        // Act
        $result = $action->calculateFinalTotal(50.00, 100.00, 0, 0);

        // Assert
        $this->assertEquals(0, $result);
    }
}
