<?php

declare(strict_types=1);

namespace Modules\Coupon\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

readonly class CouponDTO
{
    public function __construct(
        public ?int $id,
        public ?string $code,
        public ?string $name = null,
        public ?string $description = null,
        public ?string $type = null,
        public ?float $value = null,
        public ?float $minimumAmount = null,
        public ?float $maximumDiscount = null,
        public ?int $usageLimit = null,
        public ?int $usageLimitPerUser = null,
        public int $timesUsed = 0,
        public ?Carbon $startsAt = null,
        public ?Carbon $expiresAt = null,
        public ?string $status = null,
        public bool $isPublic = true,
        public bool $isStackable = false,
        public bool $freeShipping = false,
        public ?array $applicableProducts = null,
        public ?array $applicableCategories = null,
        public ?array $applicableBrands = null,
        public ?array $excludedProducts = null,
        public ?array $excludedCategories = null,
        public ?array $excludedBrands = null,
        public ?array $customerGroups = null,
        public ?array $customerIds = null,
        public ?Carbon $createdAt = null,
        public ?Carbon $updatedAt = null,
    ) {}

    public static function fromRequest(Request $request, ?int $id = null, ?\Modules\Coupon\Models\Coupon $existing = null): self
    {
        $validated = $request->validated();

        return self::fromArray([
            'id' => $id,
            'code' => $validated['code'] ?? $existing?->code,
            'name' => $validated['name'] ?? $existing?->name ?? null,
            'description' => $validated['description'] ?? $existing?->description ?? null,
            'type' => $validated['type'] ?? $existing?->type,
            'value' => $validated['value'] ?? $existing?->value ?? null,
            'minimum_amount' => $validated['minimum_amount'] ?? $existing?->minimum_amount ?? null,
            'maximum_discount' => $validated['maximum_discount'] ?? $existing?->maximum_discount ?? null,
            'usage_limit' => $validated['usage_limit'] ?? $existing?->usage_limit ?? null,
            'usage_limit_per_user' => $validated['usage_limit_per_user'] ?? $existing?->usage_limit_per_user ?? null,
            'times_used' => $existing?->times_used ?? 0,
            'starts_at' => $validated['starts_at'] ?? $existing?->starts_at ?? null,
            'expires_at' => $validated['expires_at'] ?? $existing?->expires_at ?? null,
            'status' => $validated['status'] ?? $existing?->status,
            'is_public' => $validated['is_public'] ?? $existing?->is_public ?? true,
            'is_stackable' => $validated['is_stackable'] ?? $existing?->is_stackable ?? false,
            'free_shipping' => $validated['free_shipping'] ?? $existing?->free_shipping ?? false,
            'applicable_products' => $validated['applicable_products'] ?? $existing?->applicable_products ?? null,
            'applicable_categories' => $validated['applicable_categories'] ?? $existing?->applicable_categories ?? null,
            'applicable_brands' => $validated['applicable_brands'] ?? $existing?->applicable_brands ?? null,
            'excluded_products' => $validated['excluded_products'] ?? $existing?->excluded_products ?? null,
            'excluded_categories' => $validated['excluded_categories'] ?? $existing?->excluded_categories ?? null,
            'excluded_brands' => $validated['excluded_brands'] ?? $existing?->excluded_brands ?? null,
            'customer_groups' => $validated['customer_groups'] ?? $existing?->customer_groups ?? null,
            'customer_ids' => $validated['customer_ids'] ?? $existing?->customer_ids ?? null,
        ]);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['code'] ?? null,
            $data['name'] ?? null,
            $data['description'] ?? null,
            $data['type'] ?? null,
            isset($data['value']) ? (float) $data['value'] : null,
            isset($data['minimum_amount']) ? (float) $data['minimum_amount'] : null,
            isset($data['maximum_discount']) ? (float) $data['maximum_discount'] : null,
            $data['usage_limit'] ?? null,
            $data['usage_limit_per_user'] ?? null,
            $data['times_used'] ?? 0,
            isset($data['starts_at']) ? Carbon::parse($data['starts_at']) : null,
            isset($data['expires_at']) ? Carbon::parse($data['expires_at']) : null,
            $data['status'] ?? null,
            $data['is_public'] ?? true,
            $data['is_stackable'] ?? false,
            $data['free_shipping'] ?? false,
            $data['applicable_products'] ?? null,
            $data['applicable_categories'] ?? null,
            $data['applicable_brands'] ?? null,
            $data['excluded_products'] ?? null,
            $data['excluded_categories'] ?? null,
            $data['excluded_brands'] ?? null,
            $data['customer_groups'] ?? null,
            $data['customer_ids'] ?? null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    /**
     * Convert to array for model creation/update
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'value' => $this->value,
            'minimum_amount' => $this->minimumAmount,
            'maximum_discount' => $this->maximumDiscount,
            'usage_limit' => $this->usageLimit,
            'usage_limit_per_user' => $this->usageLimitPerUser,
            'times_used' => $this->timesUsed,
            'starts_at' => $this->startsAt,
            'expires_at' => $this->expiresAt,
            'status' => $this->status,
            'is_public' => $this->isPublic,
            'is_stackable' => $this->isStackable,
            'free_shipping' => $this->freeShipping,
            'applicable_products' => $this->applicableProducts,
            'applicable_categories' => $this->applicableCategories,
            'applicable_brands' => $this->applicableBrands,
            'excluded_products' => $this->excludedProducts,
            'excluded_categories' => $this->excludedCategories,
            'excluded_brands' => $this->excludedBrands,
            'customer_groups' => $this->customerGroups,
            'customer_ids' => $this->customerIds,
        ];
    }
}
