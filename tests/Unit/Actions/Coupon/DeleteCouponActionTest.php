<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Coupon;

use Modules\Coupon\Actions\Coupon\DeleteCouponAction;
use Modules\Coupon\Models\Coupon;
use Modules\Coupon\Models\CouponUsage;
use Modules\Coupon\Repository\CouponRepository;
use Tests\Unit\Actions\ActionTestCase;

class DeleteCouponActionTest extends ActionTestCase
{
    public function test_execute_deletes_existing_coupon(): void
    {
        // Arrange
        $coupon = Coupon::factory()->fixed(10.00)->create([
            'code' => 'DELETE10',
            'status' => Coupon::STATUS_ACTIVE,
        ]);

        $repository = new CouponRepository();
        $action = new DeleteCouponAction($repository);

        // Act
        $response = $action->execute($coupon->id);

        // Assert
        $this->assertDatabaseMissing('coupons', [
            'id' => $coupon->id,
            'code' => 'DELETE10',
        ]);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_execute_deletes_coupon_and_verifies_count(): void
    {
        // Arrange
        $coupon1 = Coupon::factory()->fixed(10.00)->create();
        $coupon2 = Coupon::factory()->fixed(20.00)->create();
        $coupon3 = Coupon::factory()->fixed(30.00)->create();

        $repository = new CouponRepository();
        $action = new DeleteCouponAction($repository);

        // Act
        $action->execute($coupon2->id);

        // Assert
        $this->assertDatabaseHas('coupons', ['id' => $coupon1->id]);
        $this->assertDatabaseMissing('coupons', ['id' => $coupon2->id]);
        $this->assertDatabaseHas('coupons', ['id' => $coupon3->id]);
        $this->assertEquals(2, Coupon::count());
    }

    public function test_execute_deletes_coupon_with_usage_records(): void
    {
        // Arrange
        $coupon = Coupon::factory()->fixed(10.00)->create([
            'code' => 'COUPONWITHUSAGE',
        ]);
        
        // Create usage records using the model directly
        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id' => \Modules\User\Models\User::factory()->create()->id,
            'order_id' => \Modules\Order\Models\Order::factory()->create()->id,
            'discount_amount' => 10.00,
            'used_at' => now(),
        ]);

        $repository = new CouponRepository();
        $action = new DeleteCouponAction($repository);

        // Act
        $action->execute($coupon->id);

        // Assert
        $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
    }
}
