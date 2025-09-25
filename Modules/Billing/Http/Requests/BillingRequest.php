<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class BillingRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return array_merge([
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'type' => [
                'required',
                'string',
                'in:invoice,credit_note,debit_note,receipt,quote,estimate',
            ],
            'number' => [
                'required',
                'string',
                'max:50',
                'unique:billing_documents,number',
            ],
            'status' => [
                'required',
                'string',
                'in:draft,sent,viewed,paid,overdue,cancelled,refunded',
            ],
            'issue_date' => [
                'required',
                'date',
                'before_or_equal:due_date',
            ],
            'due_date' => [
                'required',
                'date',
                'after_or_equal:issue_date',
            ],
            'paid_date' => [
                'nullable',
                'date',
                'after_or_equal:issue_date',
            ],
            'subtotal' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'tax_amount' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'discount_amount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'total_amount' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'currency' => [
                'required',
                'string',
                'size:3',
                'regex:/^[A-Z]{3}$/',
            ],
            'exchange_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'terms_conditions' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'payment_terms' => [
                'nullable',
                'string',
                'max:500',
            ],
            'payment_method' => [
                'nullable',
                'string',
                'in:bank_transfer,credit_card,paypal,stripe,check,cash,other',
            ],
            'payment_reference' => [
                'nullable',
                'string',
                'max:100',
            ],
            'billing_address' => [
                'required',
                'array',
            ],
            'billing_address.name' => [
                'required',
                'string',
                'max:255',
            ],
            'billing_address.email' => [
                'required',
                'email',
                'max:255',
            ],
            'billing_address.phone' => [
                'nullable',
                'string',
                'max:20',
            ],
            'billing_address.address' => [
                'required',
                'string',
                'max:500',
            ],
            'billing_address.city' => [
                'required',
                'string',
                'max:100',
            ],
            'billing_address.state' => [
                'nullable',
                'string',
                'max:100',
            ],
            'billing_address.country' => [
                'required',
                'string',
                'max:100',
            ],
            'billing_address.postal_code' => [
                'required',
                'string',
                'max:20',
            ],
            'shipping_address' => [
                'nullable',
                'array',
            ],
            'shipping_address.name' => [
                'required_with:shipping_address',
                'string',
                'max:255',
            ],
            'shipping_address.email' => [
                'required_with:shipping_address',
                'email',
                'max:255',
            ],
            'shipping_address.phone' => [
                'nullable',
                'string',
                'max:20',
            ],
            'shipping_address.address' => [
                'required_with:shipping_address',
                'string',
                'max:500',
            ],
            'shipping_address.city' => [
                'required_with:shipping_address',
                'string',
                'max:100',
            ],
            'shipping_address.state' => [
                'nullable',
                'string',
                'max:100',
            ],
            'shipping_address.country' => [
                'required_with:shipping_address',
                'string',
                'max:100',
            ],
            'shipping_address.postal_code' => [
                'required_with:shipping_address',
                'string',
                'max:20',
            ],
            'items' => [
                'required',
                'array',
                'min:1',
            ],
            'items.*.description' => [
                'required',
                'string',
                'max:500',
            ],
            'items.*.quantity' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99',
            ],
            'items.*.unit_price' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'items.*.total_price' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'items.*.tax_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'items.*.tax_amount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'items.*.discount_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'items.*.discount_amount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
        ], $this->getCommonRules());
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'user_id.required' => 'User is required.',
            'user_id.exists' => 'User does not exist.',
            'type.required' => 'Document type is required.',
            'type.in' => 'Document type must be invoice, credit_note, debit_note, receipt, quote, or estimate.',
            'number.required' => 'Document number is required.',
            'number.unique' => 'This document number is already in use.',
            'number.max' => 'Document number must not exceed 50 characters.',
            'status.required' => 'Document status is required.',
            'status.in' => 'Document status must be draft, sent, viewed, paid, overdue, cancelled, or refunded.',
            'issue_date.required' => 'Issue date is required.',
            'issue_date.before_or_equal' => 'Issue date must be before or equal to due date.',
            'due_date.required' => 'Due date is required.',
            'due_date.after_or_equal' => 'Due date must be after or equal to issue date.',
            'paid_date.after_or_equal' => 'Paid date cannot be before issue date.',
            'subtotal.required' => 'Subtotal is required.',
            'subtotal.min' => 'Subtotal must be at least 0.',
            'subtotal.max' => 'Subtotal cannot exceed 999999.99.',
            'tax_amount.required' => 'Tax amount is required.',
            'tax_amount.min' => 'Tax amount must be at least 0.',
            'tax_amount.max' => 'Tax amount cannot exceed 999999.99.',
            'discount_amount.min' => 'Discount amount must be at least 0.',
            'discount_amount.max' => 'Discount amount cannot exceed 999999.99.',
            'total_amount.required' => 'Total amount is required.',
            'total_amount.min' => 'Total amount must be at least 0.',
            'total_amount.max' => 'Total amount cannot exceed 999999.99.',
            'currency.required' => 'Currency is required.',
            'currency.size' => 'Currency must be exactly 3 characters.',
            'currency.regex' => 'Currency must be in uppercase format (e.g., USD, EUR).',
            'exchange_rate.min' => 'Exchange rate must be at least 0.',
            'exchange_rate.max' => 'Exchange rate cannot exceed 999999.99.',
            'notes.max' => 'Notes must not exceed 1000 characters.',
            'terms_conditions.max' => 'Terms and conditions must not exceed 2000 characters.',
            'payment_terms.max' => 'Payment terms must not exceed 500 characters.',
            'payment_method.in' => 'Invalid payment method.',
            'payment_reference.max' => 'Payment reference must not exceed 100 characters.',
            'billing_address.required' => 'Billing address is required.',
            'billing_address.name.required' => 'Billing name is required.',
            'billing_address.name.max' => 'Billing name must not exceed 255 characters.',
            'billing_address.email.required' => 'Billing email is required.',
            'billing_address.email.email' => 'Please enter a valid billing email address.',
            'billing_address.email.max' => 'Billing email must not exceed 255 characters.',
            'billing_address.phone.max' => 'Billing phone must not exceed 20 characters.',
            'billing_address.address.required' => 'Billing address is required.',
            'billing_address.address.max' => 'Billing address must not exceed 500 characters.',
            'billing_address.city.required' => 'Billing city is required.',
            'billing_address.city.max' => 'Billing city must not exceed 100 characters.',
            'billing_address.state.max' => 'Billing state must not exceed 100 characters.',
            'billing_address.country.required' => 'Billing country is required.',
            'billing_address.country.max' => 'Billing country must not exceed 100 characters.',
            'billing_address.postal_code.required' => 'Billing postal code is required.',
            'billing_address.postal_code.max' => 'Billing postal code must not exceed 20 characters.',
            'shipping_address.name.required_with' => 'Shipping name is required when shipping address is provided.',
            'shipping_address.name.max' => 'Shipping name must not exceed 255 characters.',
            'shipping_address.email.required_with' => 'Shipping email is required when shipping address is provided.',
            'shipping_address.email.email' => 'Please enter a valid shipping email address.',
            'shipping_address.email.max' => 'Shipping email must not exceed 255 characters.',
            'shipping_address.phone.max' => 'Shipping phone must not exceed 20 characters.',
            'shipping_address.address.required_with' => 'Shipping address is required when shipping address is provided.',
            'shipping_address.address.max' => 'Shipping address must not exceed 500 characters.',
            'shipping_address.city.required_with' => 'Shipping city is required when shipping address is provided.',
            'shipping_address.city.max' => 'Shipping city must not exceed 100 characters.',
            'shipping_address.state.max' => 'Shipping state must not exceed 100 characters.',
            'shipping_address.country.required_with' => 'Shipping country is required when shipping address is provided.',
            'shipping_address.country.max' => 'Shipping country must not exceed 100 characters.',
            'shipping_address.postal_code.required_with' => 'Shipping postal code is required when shipping address is provided.',
            'shipping_address.postal_code.max' => 'Shipping postal code must not exceed 20 characters.',
            'items.required' => 'Items are required.',
            'items.min' => 'At least one item is required.',
            'items.max' => 'Maximum 100 items are allowed.',
            'items.*.description.required' => 'Item description is required.',
            'items.*.description.max' => 'Item description must not exceed 500 characters.',
            'items.*.quantity.required' => 'Item quantity is required.',
            'items.*.quantity.min' => 'Item quantity must be at least 0.01.',
            'items.*.quantity.max' => 'Item quantity cannot exceed 999999.99.',
            'items.*.unit_price.required' => 'Item unit price is required.',
            'items.*.unit_price.min' => 'Item unit price must be at least 0.',
            'items.*.unit_price.max' => 'Item unit price cannot exceed 999999.99.',
            'items.*.total_price.required' => 'Item total price is required.',
            'items.*.total_price.min' => 'Item total price must be at least 0.',
            'items.*.total_price.max' => 'Item total price cannot exceed 999999.99.',
            'items.*.tax_rate.min' => 'Item tax rate must be at least 0.',
            'items.*.tax_rate.max' => 'Item tax rate cannot exceed 100.',
            'items.*.tax_amount.min' => 'Item tax amount must be at least 0.',
            'items.*.tax_amount.max' => 'Item tax amount cannot exceed 999999.99.',
            'items.*.discount_rate.min' => 'Item discount rate must be at least 0.',
            'items.*.discount_rate.max' => 'Item discount rate cannot exceed 100.',
            'items.*.discount_amount.min' => 'Item discount amount must be at least 0.',
            'items.*.discount_amount.max' => 'Item discount amount cannot exceed 999999.99.',
        ]);
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate user
            if ($this->filled('user_id')) {
                $userId = $this->user_id;
                $user = \Modules\User\Models\User::find($userId);

                if (! $user) {
                    $validator->errors()->add(
                        'user_id',
                        'User does not exist.'
                    );
                } elseif (! $user->is_active) {
                    $validator->errors()->add(
                        'user_id',
                        'User is not active.'
                    );
                }
            }

            // Validate document number format
            if ($this->filled('number')) {
                $number = $this->number;

                if (mb_strlen($number) < 3) {
                    $validator->errors()->add(
                        'number',
                        'Document number must be at least 3 characters long.'
                    );
                }

                if (mb_strlen($number) > 50) {
                    $validator->errors()->add(
                        'number',
                        'Document number must not exceed 50 characters.'
                    );
                }
            }

            // Validate date ranges
            if ($this->filled('issue_date') && $this->filled('due_date')) {
                $issueDate = $this->issue_date;
                $dueDate = $this->due_date;

                if ($issueDate >= $dueDate) {
                    $validator->errors()->add(
                        'due_date',
                        'Due date must be after issue date.'
                    );
                }
            }

            // Validate paid date
            if ($this->filled('paid_date')) {
                $paidDate = $this->paid_date;
                $issueDate = $this->issue_date;

                if ($paidDate < $issueDate) {
                    $validator->errors()->add(
                        'paid_date',
                        'Paid date cannot be before issue date.'
                    );
                }
            }

            // Validate amount calculations
            if ($this->filled('subtotal') && $this->filled('tax_amount') && $this->filled('total_amount')) {
                $subtotal = $this->subtotal;
                $taxAmount = $this->tax_amount;
                $discountAmount = $this->discount_amount ?? 0;
                $totalAmount = $this->total_amount;

                $calculatedTotal = $subtotal + $taxAmount - $discountAmount;

                if (abs($calculatedTotal - $totalAmount) > 0.01) {
                    $validator->errors()->add(
                        'total_amount',
                        'Total amount calculation is incorrect.'
                    );
                }
            }

            // Validate discount amount
            if ($this->filled('discount_amount') && $this->filled('subtotal')) {
                $discountAmount = $this->discount_amount;
                $subtotal = $this->subtotal;

                if ($discountAmount > $subtotal) {
                    $validator->errors()->add(
                        'discount_amount',
                        'Discount amount cannot be greater than subtotal.'
                    );
                }
            }

            // Validate currency
            if ($this->filled('currency')) {
                $currency = $this->currency;
                $validCurrencies = ['USD', 'EUR', 'GBP', 'JPY', 'AUD', 'CAD', 'CHF', 'CNY', 'SEK', 'NZD'];

                if (! in_array($currency, $validCurrencies)) {
                    $validator->errors()->add(
                        'currency',
                        'Invalid currency code.'
                    );
                }
            }

            // Validate exchange rate
            if ($this->filled('exchange_rate')) {
                $exchangeRate = $this->exchange_rate;

                if ($exchangeRate <= 0) {
                    $validator->errors()->add(
                        'exchange_rate',
                        'Exchange rate must be greater than 0.'
                    );
                }

                if ($exchangeRate > 999999.99) {
                    $validator->errors()->add(
                        'exchange_rate',
                        'Exchange rate cannot exceed 999999.99.'
                    );
                }
            }

            // Validate notes
            if ($this->filled('notes') && mb_strlen($this->notes) > 1000) {
                $validator->errors()->add(
                    'notes',
                    'Notes must not exceed 1000 characters.'
                );
            }

            // Validate terms and conditions
            if ($this->filled('terms_conditions') && mb_strlen($this->terms_conditions) > 2000) {
                $validator->errors()->add(
                    'terms_conditions',
                    'Terms and conditions must not exceed 2000 characters.'
                );
            }

            // Validate payment terms
            if ($this->filled('payment_terms') && mb_strlen($this->payment_terms) > 500) {
                $validator->errors()->add(
                    'payment_terms',
                    'Payment terms must not exceed 500 characters.'
                );
            }

            // Validate payment method
            if ($this->filled('payment_method')) {
                $paymentMethod = $this->payment_method;
                $validMethods = ['bank_transfer', 'credit_card', 'paypal', 'stripe', 'check', 'cash', 'other'];

                if (! in_array($paymentMethod, $validMethods)) {
                    $validator->errors()->add(
                        'payment_method',
                        'Invalid payment method.'
                    );
                }
            }

            // Validate payment reference
            if ($this->filled('payment_reference') && mb_strlen($this->payment_reference) > 100) {
                $validator->errors()->add(
                    'payment_reference',
                    'Payment reference must not exceed 100 characters.'
                );
            }

            // Validate billing address
            if ($this->filled('billing_address')) {
                $billingAddress = $this->billing_address;

                if (mb_strlen($billingAddress['name']) < 2) {
                    $validator->errors()->add(
                        'billing_address.name',
                        'Billing name must be at least 2 characters long.'
                    );
                }

                if (mb_strlen($billingAddress['address']) < 10) {
                    $validator->errors()->add(
                        'billing_address.address',
                        'Billing address must be at least 10 characters long.'
                    );
                }

                if (mb_strlen($billingAddress['city']) < 2) {
                    $validator->errors()->add(
                        'billing_address.city',
                        'Billing city must be at least 2 characters long.'
                    );
                }

                if (mb_strlen($billingAddress['country']) < 2) {
                    $validator->errors()->add(
                        'billing_address.country',
                        'Billing country must be at least 2 characters long.'
                    );
                }

                if (mb_strlen($billingAddress['postal_code']) < 3) {
                    $validator->errors()->add(
                        'billing_address.postal_code',
                        'Billing postal code must be at least 3 characters long.'
                    );
                }
            }

            // Validate shipping address
            if ($this->filled('shipping_address')) {
                $shippingAddress = $this->shipping_address;

                if (mb_strlen($shippingAddress['name']) < 2) {
                    $validator->errors()->add(
                        'shipping_address.name',
                        'Shipping name must be at least 2 characters long.'
                    );
                }

                if (mb_strlen($shippingAddress['address']) < 10) {
                    $validator->errors()->add(
                        'shipping_address.address',
                        'Shipping address must be at least 10 characters long.'
                    );
                }

                if (mb_strlen($shippingAddress['city']) < 2) {
                    $validator->errors()->add(
                        'shipping_address.city',
                        'Shipping city must be at least 2 characters long.'
                    );
                }

                if (mb_strlen($shippingAddress['country']) < 2) {
                    $validator->errors()->add(
                        'shipping_address.country',
                        'Shipping country must be at least 2 characters long.'
                    );
                }

                if (mb_strlen($shippingAddress['postal_code']) < 3) {
                    $validator->errors()->add(
                        'shipping_address.postal_code',
                        'Shipping postal code must be at least 3 characters long.'
                    );
                }
            }

            // Validate items
            if ($this->filled('items')) {
                $items = $this->items;

                if (count($items) < 1) {
                    $validator->errors()->add(
                        'items',
                        'At least one item is required.'
                    );
                }

                if (count($items) > 100) {
                    $validator->errors()->add(
                        'items',
                        'Maximum 100 items are allowed.'
                    );
                }

                foreach ($items as $index => $item) {
                    // Validate item description
                    if (mb_strlen($item['description']) < 5) {
                        $validator->errors()->add(
                            'items.'.$index.'.description',
                            'Item description must be at least 5 characters long.'
                        );
                    }

                    // Validate item quantity
                    if ($item['quantity'] <= 0) {
                        $validator->errors()->add(
                            'items.'.$index.'.quantity',
                            'Item quantity must be greater than 0.'
                        );
                    }

                    // Validate item unit price
                    if ($item['unit_price'] < 0) {
                        $validator->errors()->add(
                            'items.'.$index.'.unit_price',
                            'Item unit price cannot be negative.'
                        );
                    }

                    // Validate item total price
                    if ($item['total_price'] < 0) {
                        $validator->errors()->add(
                            'items.'.$index.'.total_price',
                            'Item total price cannot be negative.'
                        );
                    }

                    // Validate item tax rate
                    if (isset($item['tax_rate']) && ($item['tax_rate'] < 0 || $item['tax_rate'] > 100)) {
                        $validator->errors()->add(
                            'items.'.$index.'.tax_rate',
                            'Item tax rate must be between 0 and 100.'
                        );
                    }

                    // Validate item tax amount
                    if (isset($item['tax_amount']) && $item['tax_amount'] < 0) {
                        $validator->errors()->add(
                            'items.'.$index.'.tax_amount',
                            'Item tax amount cannot be negative.'
                        );
                    }

                    // Validate item discount rate
                    if (isset($item['discount_rate']) && ($item['discount_rate'] < 0 || $item['discount_rate'] > 100)) {
                        $validator->errors()->add(
                            'items.'.$index.'.discount_rate',
                            'Item discount rate must be between 0 and 100.'
                        );
                    }

                    // Validate item discount amount
                    if (isset($item['discount_amount']) && $item['discount_amount'] < 0) {
                        $validator->errors()->add(
                            'items.'.$index.'.discount_amount',
                            'Item discount amount cannot be negative.'
                        );
                    }

                    // Validate item total price calculation
                    $quantity = $item['quantity'];
                    $unitPrice = $item['unit_price'];
                    $taxRate = $item['tax_rate'] ?? 0;
                    $discountRate = $item['discount_rate'] ?? 0;
                    $totalPrice = $item['total_price'];

                    $calculatedTotal = $quantity * $unitPrice;
                    $discountAmount = $calculatedTotal * ($discountRate / 100);
                    $taxAmount = ($calculatedTotal - $discountAmount) * ($taxRate / 100);
                    $finalTotal = $calculatedTotal - $discountAmount + $taxAmount;

                    if (abs($finalTotal - $totalPrice) > 0.01) {
                        $validator->errors()->add(
                            'items.'.$index.'.total_price',
                            'Item total price calculation is incorrect.'
                        );
                    }
                }
            }
        });
    }
}
