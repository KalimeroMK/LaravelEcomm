<?php

declare(strict_types=1);

namespace Modules\Product\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;

class ProductRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $productId = $this->route('product') ?? $this->route('id');

        return array_merge([
            'title' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_\.]+$/',
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9\-]+$/',
                Rule::unique('products', 'slug')->ignore($productId),
            ],
            'summary' => [
                'required',
                'string',
                'max:500',
            ],
            'description' => [
                'required',
                'string',
                'max:5000',
            ],
            'price' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'special_price' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
                'lt:price',
            ],
            'special_price_start' => [
                'nullable',
                'date',
                'before_or_equal:special_price_end',
            ],
            'special_price_end' => [
                'nullable',
                'date',
                'after_or_equal:special_price_start',
            ],
            'stock' => [
                'required',
                'integer',
                'min:0',
                'max:999999',
            ],
            'sku' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z0-9\-_]+$/',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'status' => [
                'required',
                'string',
                Rule::in(['active', 'inactive', 'draft']),
            ],
            'is_featured' => [
                'boolean',
            ],
            'd_deal' => [
                'boolean',
            ],
            'brand_id' => [
                'nullable',
                'integer',
                'exists:brands,id',
            ],
            'category_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
            ],
            'images' => [
                'nullable',
                'array',
                'max:10',
            ],
            'images.*' => [
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',
            ],
            'meta_title' => [
                'nullable',
                'string',
                'max:60',
            ],
            'meta_description' => [
                'nullable',
                'string',
                'max:160',
            ],
            'meta_keywords' => [
                'nullable',
                'string',
                'max:255',
            ],
        ], $this->getCommonRules());
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'title.regex' => 'Title can only contain letters, numbers, spaces, hyphens, underscores, and dots.',
            'slug.regex' => 'Slug can only contain lowercase letters, numbers, and hyphens.',
            'sku.regex' => 'SKU can only contain uppercase letters, numbers, hyphens, and underscores.',
            'special_price.lt' => 'Special price must be less than regular price.',
            'images.max' => 'You can upload maximum 10 images.',
            'images.*.max' => 'Each image must not be larger than 2MB.',
            'meta_title.max' => 'Meta title should not exceed 60 characters for SEO.',
            'meta_description.max' => 'Meta description should not exceed 160 characters for SEO.',
        ]);
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate special price logic
            if ($this->filled('special_price') && $this->filled('price') && $this->special_price >= $this->price) {
                $validator->errors()->add(
                    'special_price',
                    'Special price must be less than regular price.'
                );
            }

            // Validate special price dates
            if ($this->filled('special_price_start') && $this->filled('special_price_end') && $this->special_price_start >= $this->special_price_end) {
                $validator->errors()->add(
                    'special_price_start',
                    'Special price start date must be before end date.'
                );
            }

            // Validate stock availability
            if ($this->filled('stock') && $this->stock < 0) {
                $validator->errors()->add(
                    'stock',
                    'Stock cannot be negative.'
                );
            }
        });
    }
}
