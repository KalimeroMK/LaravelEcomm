<?php

declare(strict_types=1);

namespace Modules\Coupon\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Coupon\Models\Coupon;
use Modules\User\Models\User;
use Tests\TestCase;

abstract class CouponTestCase extends TestCase
{
    use RefreshDatabase;

    protected function createCoupon(array $overrides = []): Coupon
    {
        return Coupon::create(array_merge([
            'code' => 'TEST10',
            'name' => 'Test Coupon',
            'type' => Coupon::TYPE_PERCENT,
            'value' => 10,
            'status' => Coupon::STATUS_ACTIVE,
            'is_public' => true,
            'is_stackable' => false,
        ], $overrides));
    }

    protected function createUser(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ], $overrides));
    }

    protected function createFixedCoupon(float $value, array $overrides = []): Coupon
    {
        return $this->createCoupon(array_merge([
            'code' => 'FIXED' . (int) $value,
            'type' => Coupon::TYPE_FIXED,
            'value' => $value,
        ], $overrides));
    }

    protected function createPercentCoupon(float $value, array $overrides = []): Coupon
    {
        return $this->createCoupon(array_merge([
            'code' => 'PERCENT' . (int) $value,
            'type' => Coupon::TYPE_PERCENT,
            'value' => $value,
        ], $overrides));
    }

    protected function createFreeShippingCoupon(array $overrides = []): Coupon
    {
        return $this->createCoupon(array_merge([
            'code' => 'FREESHIP',
            'type' => Coupon::TYPE_FREE_SHIPPING,
            'value' => 0,
            'free_shipping' => true,
        ], $overrides));
    }
}
