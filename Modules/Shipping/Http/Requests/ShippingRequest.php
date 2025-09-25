<?php

declare(strict_types=1);

namespace Modules\Shipping\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class ShippingRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return array_merge([
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
                'in:fixed,weight_based,price_based,free',
            ],
            'cost' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'free_shipping_threshold' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'min_weight' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'max_weight' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'min_price' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'max_price' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'delivery_time_min' => [
                'nullable',
                'integer',
                'min:0',
                'max:365',
            ],
            'delivery_time_max' => [
                'nullable',
                'integer',
                'min:0',
                'max:365',
            ],
            'is_active' => [
                'boolean',
            ],
            'is_default' => [
                'boolean',
            ],
            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999',
            ],
            'zones' => [
                'nullable',
                'array',
            ],
            'zones.*' => [
                'string',
                'max:100',
            ],
            'countries' => [
                'nullable',
                'array',
            ],
            'countries.*' => [
                'string',
                'size:2',
                'regex:/^[A-Z]{2}$/',
            ],
            'states' => [
                'nullable',
                'array',
            ],
            'states.*' => [
                'string',
                'max:100',
            ],
            'cities' => [
                'nullable',
                'array',
            ],
            'cities.*' => [
                'string',
                'max:100',
            ],
            'postal_codes' => [
                'nullable',
                'array',
            ],
            'postal_codes.*' => [
                'string',
                'max:20',
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
            'tracking_url' => [
                'nullable',
                'url',
                'max:500',
            ],
            'tracking_number_format' => [
                'nullable',
                'string',
                'max:100',
            ],
            'insurance_available' => [
                'boolean',
            ],
            'insurance_cost' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'signature_required' => [
                'boolean',
            ],
            'adult_signature_required' => [
                'boolean',
            ],
            'cod_available' => [
                'boolean',
            ],
            'cod_cost' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'handling_fee' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'tax_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], $this->getCommonRules());
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'name.required' => 'Shipping method name is required.',
            'name.min' => 'Shipping method name must be at least 2 characters long.',
            'name.max' => 'Shipping method name must not exceed 255 characters.',
            'description.max' => 'Description must not exceed 1000 characters.',
            'type.required' => 'Shipping type is required.',
            'type.in' => 'Shipping type must be fixed, weight_based, price_based, or free.',
            'cost.min' => 'Cost must be at least 0.',
            'cost.max' => 'Cost cannot exceed 999999.99.',
            'free_shipping_threshold.min' => 'Free shipping threshold must be at least 0.',
            'free_shipping_threshold.max' => 'Free shipping threshold cannot exceed 999999.99.',
            'min_weight.min' => 'Minimum weight must be at least 0.',
            'min_weight.max' => 'Minimum weight cannot exceed 999999.99.',
            'max_weight.min' => 'Maximum weight must be at least 0.',
            'max_weight.max' => 'Maximum weight cannot exceed 999999.99.',
            'min_price.min' => 'Minimum price must be at least 0.',
            'min_price.max' => 'Minimum price cannot exceed 999999.99.',
            'max_price.min' => 'Maximum price must be at least 0.',
            'max_price.max' => 'Maximum price cannot exceed 999999.99.',
            'delivery_time_min.min' => 'Minimum delivery time must be at least 0 days.',
            'delivery_time_min.max' => 'Minimum delivery time cannot exceed 365 days.',
            'delivery_time_max.min' => 'Maximum delivery time must be at least 0 days.',
            'delivery_time_max.max' => 'Maximum delivery time cannot exceed 365 days.',
            'sort_order.min' => 'Sort order must be at least 0.',
            'sort_order.max' => 'Sort order cannot exceed 9999.',
            'zones.max' => 'Maximum 50 zones are allowed.',
            'zones.*.max' => 'Each zone must not exceed 100 characters.',
            'countries.max' => 'Maximum 200 countries are allowed.',
            'countries.*.size' => 'Each country code must be exactly 2 characters.',
            'countries.*.regex' => 'Each country code must be in uppercase format (e.g., US, CA, GB).',
            'states.max' => 'Maximum 100 states are allowed.',
            'states.*.max' => 'Each state must not exceed 100 characters.',
            'cities.max' => 'Maximum 100 cities are allowed.',
            'cities.*.max' => 'Each city must not exceed 100 characters.',
            'postal_codes.max' => 'Maximum 100 postal codes are allowed.',
            'postal_codes.*.max' => 'Each postal code must not exceed 20 characters.',
            'excluded_products.max' => 'Maximum 1000 excluded products are allowed.',
            'excluded_products.*.exists' => 'One or more selected products do not exist.',
            'excluded_categories.max' => 'Maximum 100 excluded categories are allowed.',
            'excluded_categories.*.exists' => 'One or more selected categories do not exist.',
            'excluded_brands.max' => 'Maximum 100 excluded brands are allowed.',
            'excluded_brands.*.exists' => 'One or more selected brands do not exist.',
            'tracking_url.url' => 'Please enter a valid tracking URL.',
            'tracking_url.max' => 'Tracking URL must not exceed 500 characters.',
            'tracking_number_format.max' => 'Tracking number format must not exceed 100 characters.',
            'insurance_cost.min' => 'Insurance cost must be at least 0.',
            'insurance_cost.max' => 'Insurance cost cannot exceed 999999.99.',
            'cod_cost.min' => 'COD cost must be at least 0.',
            'cod_cost.max' => 'COD cost cannot exceed 999999.99.',
            'handling_fee.min' => 'Handling fee cannot be negative.',
            'handling_fee.max' => 'Handling fee cannot exceed 999999.99.',
            'tax_rate.min' => 'Tax rate must be at least 0.',
            'tax_rate.max' => 'Tax rate cannot exceed 100.',
            'notes.max' => 'Notes must not exceed 1000 characters.',
        ]);
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate name length
            if ($this->filled('name')) {
                $name = $this->name;

                if (mb_strlen($name) < 2) {
                    $validator->errors()->add(
                        'name',
                        'Shipping method name must be at least 2 characters long.'
                    );
                }

                if (mb_strlen($name) > 255) {
                    $validator->errors()->add(
                        'name',
                        'Shipping method name must not exceed 255 characters.'
                    );
                }
            }

            // Validate description
            if ($this->filled('description') && mb_strlen($this->description) > 1000) {
                $validator->errors()->add(
                    'description',
                    'Description must not exceed 1000 characters.'
                );
            }

            // Validate cost based on type
            if ($this->filled('type') && $this->filled('cost')) {
                $type = $this->type;
                $cost = $this->cost;

                if ($type === 'free' && $cost > 0) {
                    $validator->errors()->add(
                        'cost',
                        'Free shipping method cannot have a cost.'
                    );
                }

                if ($type !== 'free' && $cost <= 0) {
                    $validator->errors()->add(
                        'cost',
                        'Non-free shipping method must have a cost greater than 0.'
                    );
                }
            }

            // Validate free shipping threshold
            if ($this->filled('free_shipping_threshold') && $this->filled('cost')) {
                $threshold = $this->free_shipping_threshold;
                $cost = $this->cost;

                if ($threshold <= $cost) {
                    $validator->errors()->add(
                        'free_shipping_threshold',
                        'Free shipping threshold must be greater than the shipping cost.'
                    );
                }
            }

            // Validate weight limits
            if ($this->filled('min_weight') && $this->filled('max_weight')) {
                $minWeight = $this->min_weight;
                $maxWeight = $this->max_weight;

                if ($minWeight >= $maxWeight) {
                    $validator->errors()->add(
                        'max_weight',
                        'Maximum weight must be greater than minimum weight.'
                    );
                }
            }

            // Validate price limits
            if ($this->filled('min_price') && $this->filled('max_price')) {
                $minPrice = $this->min_price;
                $maxPrice = $this->max_price;

                if ($minPrice >= $maxPrice) {
                    $validator->errors()->add(
                        'max_price',
                        'Maximum price must be greater than minimum price.'
                    );
                }
            }

            // Validate delivery time limits
            if ($this->filled('delivery_time_min') && $this->filled('delivery_time_max')) {
                $minTime = $this->delivery_time_min;
                $maxTime = $this->delivery_time_max;

                if ($minTime >= $maxTime) {
                    $validator->errors()->add(
                        'delivery_time_max',
                        'Maximum delivery time must be greater than minimum delivery time.'
                    );
                }
            }

            // Validate sort order
            if ($this->filled('sort_order')) {
                $sortOrder = $this->sort_order;

                if ($sortOrder < 0 || $sortOrder > 9999) {
                    $validator->errors()->add(
                        'sort_order',
                        'Sort order must be between 0 and 9999.'
                    );
                }
            }

            // Validate zones
            if ($this->filled('zones')) {
                $zones = $this->zones;

                if (count($zones) > 50) {
                    $validator->errors()->add(
                        'zones',
                        'Maximum 50 zones are allowed.'
                    );
                }

                foreach ($zones as $index => $zone) {
                    if (mb_strlen($zone) < 2) {
                        $validator->errors()->add(
                            'zones.'.$index,
                            'Each zone must be at least 2 characters long.'
                        );
                    }
                }
            }

            // Validate countries
            if ($this->filled('countries')) {
                $countries = $this->countries;

                if (count($countries) > 200) {
                    $validator->errors()->add(
                        'countries',
                        'Maximum 200 countries are allowed.'
                    );
                }

                $validCountries = [
                    'US', 'CA', 'GB', 'AU', 'DE', 'FR', 'IT', 'ES', 'NL', 'BE',
                    'CH', 'AT', 'SE', 'NO', 'DK', 'FI', 'PL', 'CZ', 'HU', 'SK',
                    'SI', 'HR', 'BG', 'RO', 'LT', 'LV', 'EE', 'IE', 'PT', 'GR',
                    'CY', 'MT', 'LU', 'IS', 'LI', 'MC', 'SM', 'VA', 'AD', 'JP',
                    'KR', 'CN', 'IN', 'SG', 'MY', 'TH', 'ID', 'PH', 'VN', 'TW',
                    'HK', 'MO', 'NZ', 'ZA', 'EG', 'MA', 'TN', 'DZ', 'LY', 'SD',
                    'ET', 'KE', 'UG', 'TZ', 'RW', 'BI', 'DJ', 'SO', 'ER', 'SS',
                    'CF', 'TD', 'NE', 'NG', 'CM', 'GQ', 'GA', 'CG', 'CD', 'AO',
                    'ZM', 'ZW', 'BW', 'NA', 'SZ', 'LS', 'MG', 'MU', 'SC', 'KM',
                    'YT', 'RE', 'MZ', 'MW', 'ZM', 'ZW', 'BW', 'NA', 'SZ', 'LS',
                ];

                foreach ($countries as $index => $country) {
                    if (! in_array($country, $validCountries)) {
                        $validator->errors()->add(
                            'countries.'.$index,
                            'Invalid country code: '.$country
                        );
                    }
                }
            }

            // Validate states
            if ($this->filled('states')) {
                $states = $this->states;

                if (count($states) > 100) {
                    $validator->errors()->add(
                        'states',
                        'Maximum 100 states are allowed.'
                    );
                }

                foreach ($states as $index => $state) {
                    if (mb_strlen($state) < 2) {
                        $validator->errors()->add(
                            'states.'.$index,
                            'Each state must be at least 2 characters long.'
                        );
                    }
                }
            }

            // Validate cities
            if ($this->filled('cities')) {
                $cities = $this->cities;

                if (count($cities) > 100) {
                    $validator->errors()->add(
                        'cities',
                        'Maximum 100 cities are allowed.'
                    );
                }

                foreach ($cities as $index => $city) {
                    if (mb_strlen($city) < 2) {
                        $validator->errors()->add(
                            'cities.'.$index,
                            'Each city must be at least 2 characters long.'
                        );
                    }
                }
            }

            // Validate postal codes
            if ($this->filled('postal_codes')) {
                $postalCodes = $this->postal_codes;

                if (count($postalCodes) > 100) {
                    $validator->errors()->add(
                        'postal_codes',
                        'Maximum 100 postal codes are allowed.'
                    );
                }

                foreach ($postalCodes as $index => $postalCode) {
                    if (mb_strlen($postalCode) < 2) {
                        $validator->errors()->add(
                            'postal_codes.'.$index,
                            'Each postal code must be at least 2 characters long.'
                        );
                    }
                }
            }

            // Validate excluded products
            if ($this->filled('excluded_products')) {
                $excludedProducts = $this->excluded_products;

                if (count($excludedProducts) > 1000) {
                    $validator->errors()->add(
                        'excluded_products',
                        'Maximum 1000 excluded products are allowed.'
                    );
                }

                foreach ($excludedProducts as $index => $productId) {
                    $product = \Modules\Product\Models\Product::find($productId);

                    if (! $product) {
                        $validator->errors()->add(
                            'excluded_products.'.$index,
                            'Product does not exist.'
                        );
                    }
                }
            }

            // Validate excluded categories
            if ($this->filled('excluded_categories')) {
                $excludedCategories = $this->excluded_categories;

                if (count($excludedCategories) > 100) {
                    $validator->errors()->add(
                        'excluded_categories',
                        'Maximum 100 excluded categories are allowed.'
                    );
                }

                foreach ($excludedCategories as $index => $categoryId) {
                    $category = \Modules\Category\Models\Category::find($categoryId);

                    if (! $category) {
                        $validator->errors()->add(
                            'excluded_categories.'.$index,
                            'Category does not exist.'
                        );
                    }
                }
            }

            // Validate excluded brands
            if ($this->filled('excluded_brands')) {
                $excludedBrands = $this->excluded_brands;

                if (count($excludedBrands) > 100) {
                    $validator->errors()->add(
                        'excluded_brands',
                        'Maximum 100 excluded brands are allowed.'
                    );
                }

                foreach ($excludedBrands as $index => $brandId) {
                    $brand = \Modules\Brand\Models\Brand::find($brandId);

                    if (! $brand) {
                        $validator->errors()->add(
                            'excluded_brands.'.$index,
                            'Brand does not exist.'
                        );
                    }
                }
            }

            // Validate tracking URL
            if ($this->filled('tracking_url')) {
                $trackingUrl = $this->tracking_url;

                if (! filter_var($trackingUrl, FILTER_VALIDATE_URL)) {
                    $validator->errors()->add(
                        'tracking_url',
                        'Please enter a valid tracking URL.'
                    );
                }
            }

            // Validate tracking number format
            if ($this->filled('tracking_number_format') && mb_strlen($this->tracking_number_format) > 100) {
                $validator->errors()->add(
                    'tracking_number_format',
                    'Tracking number format must not exceed 100 characters.'
                );
            }

            // Validate insurance cost
            if ($this->filled('insurance_cost') && $this->filled('insurance_available')) {
                $insuranceCost = $this->insurance_cost;
                $insuranceAvailable = $this->insurance_available;

                if ($insuranceAvailable && $insuranceCost <= 0) {
                    $validator->errors()->add(
                        'insurance_cost',
                        'Insurance cost must be greater than 0 when insurance is available.'
                    );
                }
            }

            // Validate COD cost
            if ($this->filled('cod_cost') && $this->filled('cod_available')) {
                $codCost = $this->cod_cost;
                $codAvailable = $this->cod_available;

                if ($codAvailable && $codCost <= 0) {
                    $validator->errors()->add(
                        'cod_cost',
                        'COD cost must be greater than 0 when COD is available.'
                    );
                }
            }

            // Validate handling fee
            if ($this->filled('handling_fee') && $this->handling_fee < 0) {
                $validator->errors()->add(
                    'handling_fee',
                    'Handling fee cannot be negative.'
                );
            }

            // Validate tax rate
            if ($this->filled('tax_rate') && ($this->tax_rate < 0 || $this->tax_rate > 100)) {
                $validator->errors()->add(
                    'tax_rate',
                    'Tax rate must be between 0 and 100.'
                );
            }

            // Validate notes
            if ($this->filled('notes') && mb_strlen($this->notes) > 1000) {
                $validator->errors()->add(
                    'notes',
                    'Notes must not exceed 1000 characters.'
                );
            }
        });
    }
}
