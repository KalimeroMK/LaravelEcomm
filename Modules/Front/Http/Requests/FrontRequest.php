<?php

declare(strict_types=1);

namespace Modules\Front\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class FrontRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return array_merge([
            'search' => [
                'nullable',
                'string',
                'max:255',
            ],
            'category' => [
                'nullable',
                'string',
                'max:100',
            ],
            'brand' => [
                'nullable',
                'string',
                'max:100',
            ],
            'price_min' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'price_max' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'sort' => [
                'nullable',
                'string',
                'in:name,price,created_at,updated_at,popularity,rating,reviews',
            ],
            'order' => [
                'nullable',
                'string',
                'in:asc,desc',
            ],
            'page' => [
                'nullable',
                'integer',
                'min:1',
                'max:1000',
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
            'filters' => [
                'nullable',
                'array',
            ],
            'filters.*' => [
                'string',
                'max:255',
            ],
            'tags' => [
                'nullable',
                'array',
            ],
            'tags.*' => [
                'string',
                'max:50',
            ],
            'attributes' => [
                'nullable',
                'array',
            ],
            'attributes.*' => [
                'string',
                'max:255',
            ],
            'availability' => [
                'nullable',
                'string',
                'in:in_stock,out_of_stock,pre_order,discontinued',
            ],
            'condition' => [
                'nullable',
                'string',
                'in:new,used,refurbished,damaged',
            ],
            'rating' => [
                'nullable',
                'numeric',
                'min:0',
                'max:5',
            ],
            'discount' => [
                'nullable',
                'boolean',
            ],
            'featured' => [
                'nullable',
                'boolean',
            ],
            'sale' => [
                'nullable',
                'boolean',
            ],
            'new' => [
                'nullable',
                'boolean',
            ],
            'trending' => [
                'nullable',
                'boolean',
            ],
            'popular' => [
                'nullable',
                'boolean',
            ],
            'recommended' => [
                'nullable',
                'boolean',
            ],
            'similar' => [
                'nullable',
                'boolean',
            ],
            'related' => [
                'nullable',
                'boolean',
            ],
            'complementary' => [
                'nullable',
                'boolean',
            ],
            'substitute' => [
                'nullable',
                'boolean',
            ],
            'alternative' => [
                'nullable',
                'boolean',
            ],
            'upgrade' => [
                'nullable',
                'boolean',
            ],
            'downgrade' => [
                'nullable',
                'boolean',
            ],
            'bundle' => [
                'nullable',
                'boolean',
            ],
            'kit' => [
                'nullable',
                'boolean',
            ],
            'set' => [
                'nullable',
                'boolean',
            ],
            'collection' => [
                'nullable',
                'boolean',
            ],
            'series' => [
                'nullable',
                'boolean',
            ],
            'line' => [
                'nullable',
                'boolean',
            ],
            'family' => [
                'nullable',
                'boolean',
            ],
            'group' => [
                'nullable',
                'boolean',
            ],
            'type' => [
                'nullable',
                'string',
                'max:100',
            ],
            'subtype' => [
                'nullable',
                'string',
                'max:100',
            ],
            'variant' => [
                'nullable',
                'string',
                'max:100',
            ],
            'model' => [
                'nullable',
                'string',
                'max:100',
            ],
            'version' => [
                'nullable',
                'string',
                'max:100',
            ],
            'edition' => [
                'nullable',
                'string',
                'max:100',
            ],
            'release' => [
                'nullable',
                'string',
                'max:100',
            ],
            'generation' => [
                'nullable',
                'string',
                'max:100',
            ],
            'series_number' => [
                'nullable',
                'string',
                'max:100',
            ],
            'part_number' => [
                'nullable',
                'string',
                'max:100',
            ],
            'sku' => [
                'nullable',
                'string',
                'max:100',
            ],
            'upc' => [
                'nullable',
                'string',
                'max:100',
            ],
            'ean' => [
                'nullable',
                'string',
                'max:100',
            ],
            'isbn' => [
                'nullable',
                'string',
                'max:100',
            ],
            'asin' => [
                'nullable',
                'string',
                'max:100',
            ],
            'gtin' => [
                'nullable',
                'string',
                'max:100',
            ],
            'mpn' => [
                'nullable',
                'string',
                'max:100',
            ],
            'jan' => [
                'nullable',
                'string',
                'max:100',
            ],
            'itf' => [
                'nullable',
                'string',
                'max:100',
            ],
            'sscc' => [
                'nullable',
                'string',
                'max:100',
            ],
            'gs1' => [
                'nullable',
                'string',
                'max:100',
            ],
            'other' => [
                'nullable',
                'string',
                'max:100',
            ],
        ], $this->getCommonRules());
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'search.min' => 'Search query must be at least 2 characters long.',
            'search.max' => 'Search query must not exceed 255 characters.',
            'category.max' => 'Category must not exceed 100 characters.',
            'brand.max' => 'Brand must not exceed 100 characters.',
            'price_min.min' => 'Minimum price must be at least 0.',
            'price_min.max' => 'Minimum price cannot exceed 999999.99.',
            'price_max.min' => 'Maximum price must be at least 0.',
            'price_max.max' => 'Maximum price cannot exceed 999999.99.',
            'sort.in' => 'Invalid sort option.',
            'order.in' => 'Order must be asc or desc.',
            'page.min' => 'Page must be at least 1.',
            'page.max' => 'Page cannot exceed 1000.',
            'per_page.min' => 'Per page must be at least 1.',
            'per_page.max' => 'Per page cannot exceed 100.',
            'filters.max' => 'Maximum 50 filters are allowed.',
            'filters.*.max' => 'Each filter must not exceed 255 characters.',
            'tags.max' => 'Maximum 20 tags are allowed.',
            'tags.*.max' => 'Each tag must not exceed 50 characters.',
            'attributes.max' => 'Maximum 50 attributes are allowed.',
            'attributes.*.max' => 'Each attribute must not exceed 255 characters.',
            'availability.in' => 'Invalid availability option.',
            'condition.in' => 'Invalid condition option.',
            'rating.min' => 'Rating must be at least 0.',
            'rating.max' => 'Rating cannot exceed 5.',
        ]);
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate search query
            if ($this->filled('search')) {
                $search = $this->search;

                if (mb_strlen($search) < 2) {
                    $validator->errors()->add(
                        'search',
                        'Search query must be at least 2 characters long.'
                    );
                }

                if (mb_strlen($search) > 255) {
                    $validator->errors()->add(
                        'search',
                        'Search query must not exceed 255 characters.'
                    );
                }
            }

            // Validate price range
            if ($this->filled('price_min') && $this->filled('price_max')) {
                $priceMin = $this->price_min;
                $priceMax = $this->price_max;

                if ($priceMin >= $priceMax) {
                    $validator->errors()->add(
                        'price_max',
                        'Maximum price must be greater than minimum price.'
                    );
                }
            }

            // Validate sort options
            if ($this->filled('sort')) {
                $sort = $this->sort;
                $validSorts = ['name', 'price', 'created_at', 'updated_at', 'popularity', 'rating', 'reviews'];

                if (! in_array($sort, $validSorts)) {
                    $validator->errors()->add(
                        'sort',
                        'Invalid sort option.'
                    );
                }
            }

            // Validate order
            if ($this->filled('order')) {
                $order = $this->order;
                $validOrders = ['asc', 'desc'];

                if (! in_array($order, $validOrders)) {
                    $validator->errors()->add(
                        'order',
                        'Order must be asc or desc.'
                    );
                }
            }

            // Validate page
            if ($this->filled('page')) {
                $page = $this->page;

                if ($page < 1) {
                    $validator->errors()->add(
                        'page',
                        'Page must be at least 1.'
                    );
                }

                if ($page > 1000) {
                    $validator->errors()->add(
                        'page',
                        'Page cannot exceed 1000.'
                    );
                }
            }

            // Validate per page
            if ($this->filled('per_page')) {
                $perPage = $this->per_page;

                if ($perPage < 1) {
                    $validator->errors()->add(
                        'per_page',
                        'Per page must be at least 1.'
                    );
                }

                if ($perPage > 100) {
                    $validator->errors()->add(
                        'per_page',
                        'Per page cannot exceed 100.'
                    );
                }
            }

            // Validate filters
            if ($this->filled('filters')) {
                $filters = $this->filters;

                if (count($filters) > 50) {
                    $validator->errors()->add(
                        'filters',
                        'Maximum 50 filters are allowed.'
                    );
                }

                foreach ($filters as $index => $filter) {
                    if (mb_strlen($filter) < 2) {
                        $validator->errors()->add(
                            'filters.'.$index,
                            'Each filter must be at least 2 characters long.'
                        );
                    }
                }
            }

            // Validate tags
            if ($this->filled('tags')) {
                $tags = $this->tags;

                if (count($tags) > 20) {
                    $validator->errors()->add(
                        'tags',
                        'Maximum 20 tags are allowed.'
                    );
                }

                foreach ($tags as $index => $tag) {
                    if (mb_strlen($tag) < 2) {
                        $validator->errors()->add(
                            'tags.'.$index,
                            'Each tag must be at least 2 characters long.'
                        );
                    }
                }
            }

            // Validate attributes
            if ($this->filled('attributes')) {
                $attributes = $this->attributes;

                if (count($attributes) > 50) {
                    $validator->errors()->add(
                        'attributes',
                        'Maximum 50 attributes are allowed.'
                    );
                }

                foreach ($attributes as $index => $attribute) {
                    if (mb_strlen($attribute) < 2) {
                        $validator->errors()->add(
                            'attributes.'.$index,
                            'Each attribute must be at least 2 characters long.'
                        );
                    }
                }
            }

            // Validate availability
            if ($this->filled('availability')) {
                $availability = $this->availability;
                $validAvailabilities = ['in_stock', 'out_of_stock', 'pre_order', 'discontinued'];

                if (! in_array($availability, $validAvailabilities)) {
                    $validator->errors()->add(
                        'availability',
                        'Invalid availability option.'
                    );
                }
            }

            // Validate condition
            if ($this->filled('condition')) {
                $condition = $this->condition;
                $validConditions = ['new', 'used', 'refurbished', 'damaged'];

                if (! in_array($condition, $validConditions)) {
                    $validator->errors()->add(
                        'condition',
                        'Invalid condition option.'
                    );
                }
            }

            // Validate rating
            if ($this->filled('rating')) {
                $rating = $this->rating;

                if ($rating < 0) {
                    $validator->errors()->add(
                        'rating',
                        'Rating must be at least 0.'
                    );
                }

                if ($rating > 5) {
                    $validator->errors()->add(
                        'rating',
                        'Rating cannot exceed 5.'
                    );
                }
            }
        });
    }
}
