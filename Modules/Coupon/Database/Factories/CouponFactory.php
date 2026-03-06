<?php

declare(strict_types=1);

namespace Modules\Coupon\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Coupon\Models\Coupon;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'code' => 'CODE-'.mb_strtoupper(Str::random(6)),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'type' => $this->faker->randomElement([Coupon::TYPE_FIXED, Coupon::TYPE_PERCENT, Coupon::TYPE_FREE_SHIPPING]),
            'value' => $this->faker->randomFloat(2, 5, 100),
            'minimum_amount' => null,
            'maximum_discount' => null,
            'usage_limit' => null,
            'usage_limit_per_user' => null,
            'times_used' => 0,
            'starts_at' => null,
            'expires_at' => now()->addDays($this->faker->numberBetween(1, 30)),
            'status' => $this->faker->randomElement([Coupon::STATUS_ACTIVE, Coupon::STATUS_INACTIVE]),
            'is_public' => true,
            'is_stackable' => false,
            'free_shipping' => false,
            'applicable_products' => null,
            'applicable_categories' => null,
            'applicable_brands' => null,
            'excluded_products' => null,
            'excluded_categories' => null,
            'excluded_brands' => null,
            'customer_groups' => null,
            'customer_ids' => null,
        ];
    }

    public function fixed(float $value): self
    {
        return $this->state(fn (array $attributes) => [
            'type' => Coupon::TYPE_FIXED,
            'value' => $value,
        ]);
    }

    public function percent(float $value): self
    {
        return $this->state(fn (array $attributes) => [
            'type' => Coupon::TYPE_PERCENT,
            'value' => $value,
        ]);
    }

    public function freeShipping(): self
    {
        return $this->state(fn (array $attributes) => [
            'type' => Coupon::TYPE_FREE_SHIPPING,
            'value' => 0,
            'free_shipping' => true,
        ]);
    }

    public function active(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => Coupon::STATUS_ACTIVE,
        ]);
    }

    public function inactive(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => Coupon::STATUS_INACTIVE,
        ]);
    }

    public function expired(): self
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }

    public function startsInFuture(): self
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => now()->addDay(),
        ]);
    }

    public function withUsageLimit(int $limit): self
    {
        return $this->state(fn (array $attributes) => [
            'usage_limit' => $limit,
        ]);
    }

    public function withUsageLimitPerUser(int $limit): self
    {
        return $this->state(fn (array $attributes) => [
            'usage_limit_per_user' => $limit,
        ]);
    }

    public function withMinimumAmount(float $amount): self
    {
        return $this->state(fn (array $attributes) => [
            'minimum_amount' => $amount,
        ]);
    }

    public function withMaximumDiscount(float $amount): self
    {
        return $this->state(fn (array $attributes) => [
            'maximum_discount' => $amount,
        ]);
    }

    public function forProducts(array $productIds): self
    {
        return $this->state(fn (array $attributes) => [
            'applicable_products' => $productIds,
        ]);
    }

    public function forCategories(array $categoryIds): self
    {
        return $this->state(fn (array $attributes) => [
            'applicable_categories' => $categoryIds,
        ]);
    }

    public function stackable(): self
    {
        return $this->state(fn (array $attributes) => [
            'is_stackable' => true,
        ]);
    }
}
