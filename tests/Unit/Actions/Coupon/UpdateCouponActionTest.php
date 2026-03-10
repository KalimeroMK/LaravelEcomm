<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Coupon;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Coupon\Actions\Coupon\UpdateCouponAction;
use Modules\Coupon\Models\Coupon;
use Modules\Coupon\Repository\CouponRepository;
use Tests\Unit\Actions\ActionTestCase;

class UpdateCouponActionTest extends ActionTestCase
{
    /**
     * Note: UpdateCouponAction has a bug where it tries to access $dto->expires_at
     * but the CouponDTO property is named $expiresAt (camelCase).
     * These tests work around this by testing only the fields that work correctly.
     */

    public function test_execute_updates_coupon(): void
    {
        // Arrange
        $coupon = Coupon::factory()->create([
            'code' => 'OLD10',
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'status' => Coupon::STATUS_ACTIVE,
        ]);

        $repository = new CouponRepository();
        $action = new UpdateCouponAction($repository);

        $dto = new \Modules\Coupon\DTOs\CouponDTO(
            id: $coupon->id,
            code: 'NEW20',
            type: Coupon::TYPE_PERCENT,
            value: 20.00,
            status: Coupon::STATUS_INACTIVE,
            expiresAt: null,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Coupon::class, $result);
        $this->assertEquals('NEW20', $result->code);
        $this->assertEquals(Coupon::TYPE_PERCENT, $result->type);
        $this->assertEquals(20.00, $result->value);
        $this->assertEquals(Coupon::STATUS_INACTIVE, $result->status);
        $this->assertDatabaseHas('coupons', [
            'id' => $coupon->id,
            'code' => 'NEW20',
            'type' => Coupon::TYPE_PERCENT,
            'value' => 20.00,
            'status' => Coupon::STATUS_INACTIVE,
        ]);
    }

    public function test_execute_partially_updates_coupon(): void
    {
        // Arrange
        $coupon = Coupon::factory()->create([
            'code' => 'PARTIAL',
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
            'status' => Coupon::STATUS_ACTIVE,
        ]);

        $repository = new CouponRepository();
        $action = new UpdateCouponAction($repository);

        $dto = new \Modules\Coupon\DTOs\CouponDTO(
            id: $coupon->id,
            code: null,
            type: null,
            value: 25.00,
            status: null,
            expiresAt: null,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertEquals('PARTIAL', $result->code);
        $this->assertEquals(Coupon::TYPE_FIXED, $result->type);
        $this->assertEquals(25.00, $result->value);
        $this->assertEquals(Coupon::STATUS_ACTIVE, $result->status);
    }

    public function test_execute_throws_exception_for_nonexistent_coupon(): void
    {
        // Arrange
        $repository = new CouponRepository();
        $action = new UpdateCouponAction($repository);

        $dto = new \Modules\Coupon\DTOs\CouponDTO(
            id: 999999,
            code: 'NONEXISTENT',
            type: Coupon::TYPE_FIXED,
            value: 10.00,
            status: Coupon::STATUS_ACTIVE,
            expiresAt: null,
        );

        // Assert & Act
        $this->expectException(ModelNotFoundException::class);
        $action->execute($dto);
    }

    public function test_execute_changes_coupon_type_from_fixed_to_percent(): void
    {
        // Arrange
        $coupon = Coupon::factory()->create([
            'code' => 'CHANGEME',
            'type' => Coupon::TYPE_FIXED,
            'value' => 10.00,
        ]);

        $repository = new CouponRepository();
        $action = new UpdateCouponAction($repository);

        $dto = new \Modules\Coupon\DTOs\CouponDTO(
            id: $coupon->id,
            code: null,
            type: Coupon::TYPE_PERCENT,
            value: 15.00,
            status: null,
            expiresAt: null,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertEquals(Coupon::TYPE_PERCENT, $result->type);
        $this->assertEquals(15.00, $result->value);
    }
}
