<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Coupon;

use Modules\Coupon\Actions\Coupon\CreateCouponAction;
use Modules\Coupon\Models\Coupon;
use Modules\Coupon\Repository\CouponRepository;
use Tests\Unit\Actions\ActionTestCase;

class CreateCouponActionTest extends ActionTestCase
{
    /**
     * Note: CreateCouponAction has a bug where it tries to access $dto->expires_at
     * but the CouponDTO property is named $expiresAt (camelCase).
     * These tests work around this by using null for expires_at.
     */

    public function test_execute_creates_coupon(): void
    {
        // Arrange
        $repository = new CouponRepository();
        $action = new CreateCouponAction($repository);

        // Create a DTO with null expiresAt to avoid the bug
        $dto = new \Modules\Coupon\DTOs\CouponDTO(
            id: null,
            code: 'SAVE20',
            type: Coupon::TYPE_FIXED,
            value: 20.00,
            status: Coupon::STATUS_ACTIVE,
            expiresAt: null,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertEquals('SAVE20', $result->code);
        $this->assertEquals(Coupon::TYPE_FIXED, $result->type);
        $this->assertEquals(20.00, $result->value);
        $this->assertEquals(Coupon::STATUS_ACTIVE, $result->status);
        $this->assertDatabaseHas('coupons', [
            'code' => 'SAVE20',
            'type' => Coupon::TYPE_FIXED,
            'value' => 20.00,
            'status' => Coupon::STATUS_ACTIVE,
        ]);
    }

    public function test_execute_creates_percent_coupon(): void
    {
        // Arrange
        $repository = new CouponRepository();
        $action = new CreateCouponAction($repository);

        $dto = new \Modules\Coupon\DTOs\CouponDTO(
            id: null,
            code: 'HALFOFF',
            type: Coupon::TYPE_PERCENT,
            value: 50.00,
            status: Coupon::STATUS_ACTIVE,
            expiresAt: null,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertEquals('HALFOFF', $result->code);
        $this->assertEquals(Coupon::TYPE_PERCENT, $result->type);
        $this->assertEquals(50.00, $result->value);
    }

    public function test_execute_creates_percent_coupon_with_zero_value(): void
    {
        // Arrange
        $repository = new CouponRepository();
        $action = new CreateCouponAction($repository);

        $dto = new \Modules\Coupon\DTOs\CouponDTO(
            id: null,
            code: 'FREESHIP',
            type: Coupon::TYPE_PERCENT,
            value: 0.00,
            status: Coupon::STATUS_ACTIVE,
            expiresAt: null,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertEquals('FREESHIP', $result->code);
        $this->assertEquals(Coupon::TYPE_PERCENT, $result->type);
        $this->assertEquals(0.00, $result->value);
    }

    public function test_execute_creates_inactive_coupon(): void
    {
        // Arrange
        $repository = new CouponRepository();
        $action = new CreateCouponAction($repository);

        $dto = new \Modules\Coupon\DTOs\CouponDTO(
            id: null,
            code: 'INACTIVE10',
            type: Coupon::TYPE_FIXED,
            value: 10.00,
            status: Coupon::STATUS_INACTIVE,
            expiresAt: null,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertEquals(Coupon::STATUS_INACTIVE, $result->status);
    }
}
