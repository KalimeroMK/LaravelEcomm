<?php

declare(strict_types=1);

namespace Modules\Coupon\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;

class CouponRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $couponId = $this->route('coupon') ?? $this->route('id');

        return array_merge([
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9\-_]+$/',
                Rule::unique('coupons', 'code')->ignore($couponId),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'type' => [
                'required',
                'string',
                'in:fixed,percentage,free_shipping',
            ],
            'value' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'minimum_amount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'maximum_discount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'usage_limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:999999',
            ],
            'usage_limit_per_user' => [
                'nullable',
                'integer',
                'min:1',
                'max:999999',
            ],
            'used_count' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'starts_at' => [
                'required',
                'date',
                'before_or_equal:expires_at',
            ],
            'expires_at' => [
                'required',
                'date',
                'after_or_equal:starts_at',
            ],
            'is_active' => [
                'boolean',
            ],
            'is_public' => [
                'boolean',
            ],
            'applicable_products' => [
                'nullable',
                'array',
            ],
            'applicable_products.*' => [
                'integer',
                'exists:products,id',
            ],
            'applicable_categories' => [
                'nullable',
                'array',
            ],
            'applicable_categories.*' => [
                'integer',
                'exists:categories,id',
            ],
            'applicable_brands' => [
                'nullable',
                'array',
            ],
            'applicable_brands.*' => [
                'integer',
                'exists:brands,id',
            ],
            'excluded_products' => [
                'nullable',
                'array',
            ],
            'excluded_products.*' => [
                'integer',
                'exists:products,id',
            ],
            'excluded_categories' => [
                'nullable',
                'array',
            ],
            'excluded_categories.*' => [
                'integer',
                'exists:categories,id',
            ],
            'excluded_brands' => [
                'nullable',
                'array',
            ],
            'excluded_brands.*' => [
                'integer',
                'exists:brands,id',
            ],
        ], $this->getCommonRules());
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'code.regex' => 'Coupon code can only contain uppercase letters, numbers, hyphens, and underscores.',
            'code.unique' => 'This coupon code is already in use.',
            'type.in' => 'Coupon type must be fixed, percentage, or free_shipping.',
            'value.min' => 'Coupon value must be at least 0.',
            'value.max' => 'Coupon value cannot exceed 999999.99.',
            'minimum_amount.min' => 'Minimum amount must be at least 0.',
            'minimum_amount.max' => 'Minimum amount cannot exceed 999999.99.',
            'maximum_discount.min' => 'Maximum discount must be at least 0.',
            'maximum_discount.max' => 'Maximum discount cannot exceed 999999.99.',
            'usage_limit.min' => 'Usage limit must be at least 1.',
            'usage_limit.max' => 'Usage limit cannot exceed 999999.',
            'usage_limit_per_user.min' => 'Usage limit per user must be at least 1.',
            'usage_limit_per_user.max' => 'Usage limit per user cannot exceed 999999.',
            'used_count.min' => 'Used count must be at least 0.',
            'used_count.max' => 'Used count cannot exceed 999999.',
            'starts_at.required' => 'Start date is required.',
            'starts_at.before_or_equal' => 'Start date must be before or equal to expiration date.',
            'expires_at.required' => 'Expiration date is required.',
            'expires_at.after_or_equal' => 'Expiration date must be after or equal to start date.',
            'applicable_products.*.exists' => 'One or more selected products do not exist.',
            'applicable_categories.*.exists' => 'One or more selected categories do not exist.',
            'applicable_brands.*.exists' => 'One or more selected brands do not exist.',
            'excluded_products.*.exists' => 'One or more selected excluded products do not exist.',
            'excluded_categories.*.exists' => 'One or more selected excluded categories do not exist.',
            'excluded_brands.*.exists' => 'One or more selected excluded brands do not exist.',
        ]);
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate coupon value based on type
            if ($this->filled('type') && $this->filled('value')) {
                $type = $this->type;
                $value = $this->value;

                if ($type === 'percentage' && ($value < 0 || $value > 100)) {
                    $validator->errors()->add(
                        'value',
                        'Percentage value must be between 0 and 100.'
                    );
                }

                if ($type === 'fixed' && $value <= 0) {
                    $validator->errors()->add(
                        'value',
                        'Fixed value must be greater than 0.'
                    );
                }

                if ($type === 'free_shipping' && $value !== 0) {
                    $validator->errors()->add(
                        'value',
                        'Free shipping coupon value must be 0.'
                    );
                }
            }

            // Validate minimum amount
            if ($this->filled('minimum_amount') && $this->filled('value')) {
                $minimumAmount = $this->minimum_amount;
                $value = $this->value;

                if ($this->type === 'fixed' && $minimumAmount <= $value) {
                    $validator->errors()->add(
                        'minimum_amount',
                        'Minimum amount must be greater than the coupon value for fixed discounts.'
                    );
                }
            }

            // Validate maximum discount
            if ($this->filled('maximum_discount') && $this->filled('value')) {
                $maximumDiscount = $this->maximum_discount;
                $value = $this->value;

                if ($this->type === 'percentage' && $maximumDiscount <= $value) {
                    $validator->errors()->add(
                        'maximum_discount',
                        'Maximum discount must be greater than the coupon value for percentage discounts.'
                    );
                }
            }

            // Validate usage limits
            if ($this->filled('usage_limit') && $this->filled('usage_limit_per_user')) {
                $usageLimit = $this->usage_limit;
                $usageLimitPerUser = $this->usage_limit_per_user;

                if ($usageLimitPerUser > $usageLimit) {
                    $validator->errors()->add(
                        'usage_limit_per_user',
                        'Usage limit per user cannot be greater than total usage limit.'
                    );
                }
            }

            // Validate used count
            if ($this->filled('used_count') && $this->filled('usage_limit')) {
                $usedCount = $this->used_count;
                $usageLimit = $this->usage_limit;

                if ($usedCount > $usageLimit) {
                    $validator->errors()->add(
                        'used_count',
                        'Used count cannot be greater than usage limit.'
                    );
                }
            }

            // Validate date ranges
            if ($this->filled('starts_at') && $this->filled('expires_at')) {
                $startsAt = $this->starts_at;
                $expiresAt = $this->expires_at;

                if ($startsAt >= $expiresAt) {
                    $validator->errors()->add(
                        'expires_at',
                        'Expiration date must be after start date.'
                    );
                }

                if ($startsAt < now()) {
                    $validator->errors()->add(
                        'starts_at',
                        'Start date cannot be in the past.'
                    );
                }
            }

            // Validate product/category/brand exclusions
            if ($this->filled('applicable_products') && $this->filled('excluded_products')) {
                $applicableProducts = $this->applicable_products;
                $excludedProducts = $this->excluded_products;

                $intersection = array_intersect($applicableProducts, $excludedProducts);
                if ($intersection !== []) {
                    $validator->errors()->add(
                        'excluded_products',
                        'Excluded products cannot be in the applicable products list.'
                    );
                }
            }
        });
    }
}
