<?php

declare(strict_types=1);

namespace Modules\Cart\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class CartRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return array_merge([
            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:999',
            ],
            'user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
            'session_id' => [
                'nullable',
                'string',
                'max:255',
            ],
            'attributes' => [
                'nullable',
                'array',
            ],
            'attributes.*' => [
                'string',
                'max:255',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:500',
            ],
        ], $this->getCommonRules());
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'product_id.required' => 'Product ID is required.',
            'product_id.exists' => 'Selected product does not exist.',
            'quantity.required' => 'Quantity is required.',
            'quantity.min' => 'Quantity must be at least 1.',
            'quantity.max' => 'Quantity cannot exceed 999.',
            'user_id.exists' => 'Selected user does not exist.',
            'session_id.max' => 'Session ID must not exceed 255 characters.',
            'attributes.max' => 'Maximum 10 attributes are allowed.',
            'attributes.*.max' => 'Attribute value must not exceed 255 characters.',
            'notes.max' => 'Notes must not exceed 500 characters.',
        ]);
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate product availability
            if ($this->filled('product_id')) {
                $product = \Modules\Product\Models\Product::find($this->product_id);

                if (! $product) {
                    $validator->errors()->add(
                        'product_id',
                        'Product not found.'
                    );
                } elseif (! $product->is_active) {
                    $validator->errors()->add(
                        'product_id',
                        'Product is not available.'
                    );
                } elseif ($product->stock < $this->quantity) {
                    $validator->errors()->add(
                        'quantity',
                        'Insufficient stock. Available: '.$product->stock.' units.'
                    );
                }
            }

            // Validate user or session
            if (! $this->filled('user_id') && ! $this->filled('session_id')) {
                $validator->errors()->add(
                    'user_id',
                    'Either user_id or session_id must be provided.'
                );
            }

            // Validate quantity limits
            if ($this->filled('quantity')) {
                $quantity = $this->quantity;

                if ($quantity < 1) {
                    $validator->errors()->add(
                        'quantity',
                        'Quantity must be at least 1.'
                    );
                }

                if ($quantity > 999) {
                    $validator->errors()->add(
                        'quantity',
                        'Quantity cannot exceed 999.'
                    );
                }
            }

            // Validate attributes
            if ($this->filled('attributes')) {
                $attributes = $this->attributes;

                if (count($attributes) > 10) {
                    $validator->errors()->add(
                        'attributes',
                        'Maximum 10 attributes are allowed.'
                    );
                }

                foreach ($attributes as $key => $value) {
                    if (mb_strlen($key) > 50) {
                        $validator->errors()->add(
                            'attributes.'.$key,
                            'Attribute key must not exceed 50 characters.'
                        );
                    }

                    if (mb_strlen($value) > 255) {
                        $validator->errors()->add(
                            'attributes.'.$key,
                            'Attribute value must not exceed 255 characters.'
                        );
                    }
                }
            }

            // Validate notes
            if ($this->filled('notes') && mb_strlen($this->notes) > 500) {
                $validator->errors()->add(
                    'notes',
                    'Notes must not exceed 500 characters.'
                );
            }
        });
    }
}
