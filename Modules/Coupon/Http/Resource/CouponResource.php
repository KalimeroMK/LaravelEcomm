<?php

declare(strict_types=1);

namespace Modules\Coupon\Http\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Coupon\Models\Coupon;

/** @mixin Coupon */
class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed> Array of various types depending on the property.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'value' => $this->value,
            'minimum_amount' => $this->minimum_amount,
            'maximum_discount' => $this->maximum_discount,
            'usage_limit' => $this->usage_limit,
            'usage_limit_per_user' => $this->usage_limit_per_user,
            'times_used' => $this->times_used,
            'starts_at' => $this->starts_at,
            'expires_at' => $this->expires_at,
            'status' => $this->status,
            'is_public' => $this->is_public,
            'is_stackable' => $this->is_stackable,
            'free_shipping' => $this->free_shipping,
            'applicable_products' => $this->applicable_products,
            'applicable_categories' => $this->applicable_categories,
            'applicable_brands' => $this->applicable_brands,
            'excluded_products' => $this->excluded_products,
            'excluded_categories' => $this->excluded_categories,
            'excluded_brands' => $this->excluded_brands,
            'customer_groups' => $this->customer_groups,
            'customer_ids' => $this->customer_ids,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // Computed fields
            'is_valid' => $this->isValid(),
            'usage_remaining' => $this->usage_limit ? $this->usage_limit - $this->times_used : null,
            'discount_text' => $this->getDiscountText(),
        ];
    }
}
