<?php

declare(strict_types=1);

namespace Modules\Order\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class OrderRequest extends BaseRequest
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
            'order_number' => [
                'required',
                'string',
                'max:50',
                'unique:orders,order_number',
            ],
            'status' => [
                'required',
                'string',
                'in:pending,processing,shipped,delivered,cancelled,refunded',
            ],
            'payment_status' => [
                'required',
                'string',
                'in:pending,paid,unpaid,refunded,partially_refunded',
            ],
            'payment_method' => [
                'required',
                'string',
                'in:cod,paypal,stripe,bank_transfer,credit_card',
            ],
            'shipping_method' => [
                'required',
                'string',
                'max:100',
            ],
            'sub_total' => [
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
            'shipping_cost' => [
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
            'coupon_id' => [
                'nullable',
                'integer',
                'exists:coupons,id',
            ],
            'coupon_code' => [
                'nullable',
                'string',
                'max:50',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'shipping_address' => [
                'required',
                'array',
            ],
            'shipping_address.name' => [
                'required',
                'string',
                'max:255',
            ],
            'shipping_address.email' => [
                'required',
                'email',
                'max:255',
            ],
            'shipping_address.phone' => [
                'required',
                'string',
                'max:20',
            ],
            'shipping_address.address' => [
                'required',
                'string',
                'max:500',
            ],
            'shipping_address.city' => [
                'required',
                'string',
                'max:100',
            ],
            'shipping_address.country' => [
                'required',
                'string',
                'max:100',
            ],
            'shipping_address.postal_code' => [
                'required',
                'string',
                'max:20',
            ],
            'billing_address' => [
                'nullable',
                'array',
            ],
            'billing_address.name' => [
                'required_with:billing_address',
                'string',
                'max:255',
            ],
            'billing_address.email' => [
                'required_with:billing_address',
                'email',
                'max:255',
            ],
            'billing_address.phone' => [
                'required_with:billing_address',
                'string',
                'max:20',
            ],
            'billing_address.address' => [
                'required_with:billing_address',
                'string',
                'max:500',
            ],
            'billing_address.city' => [
                'required_with:billing_address',
                'string',
                'max:100',
            ],
            'billing_address.country' => [
                'required_with:billing_address',
                'string',
                'max:100',
            ],
            'billing_address.postal_code' => [
                'required_with:billing_address',
                'string',
                'max:20',
            ],
        ], $this->getCommonRules());
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'order_number.unique' => 'This order number already exists.',
            'shipping_address.required' => 'Shipping address is required.',
            'shipping_address.name.required' => 'Shipping name is required.',
            'shipping_address.email.required' => 'Shipping email is required.',
            'shipping_address.phone.required' => 'Shipping phone is required.',
            'shipping_address.address.required' => 'Shipping address is required.',
            'shipping_address.city.required' => 'Shipping city is required.',
            'shipping_address.country.required' => 'Shipping country is required.',
            'shipping_address.postal_code.required' => 'Shipping postal code is required.',
            'billing_address.name.required_with' => 'Billing name is required when billing address is provided.',
            'billing_address.email.required_with' => 'Billing email is required when billing address is provided.',
            'billing_address.phone.required_with' => 'Billing phone is required when billing address is provided.',
            'billing_address.address.required_with' => 'Billing address is required when billing address is provided.',
            'billing_address.city.required_with' => 'Billing city is required when billing address is provided.',
            'billing_address.country.required_with' => 'Billing country is required when billing address is provided.',
            'billing_address.postal_code.required_with' => 'Billing postal code is required when billing address is provided.',
        ]);
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate total amount calculation
            $subTotal = $this->sub_total ?? 0;
            $taxAmount = $this->tax_amount ?? 0;
            $shippingCost = $this->shipping_cost ?? 0;
            $discountAmount = $this->discount_amount ?? 0;
            $totalAmount = $this->total_amount ?? 0;

            $calculatedTotal = $subTotal + $taxAmount + $shippingCost - $discountAmount;

            if (abs($calculatedTotal - $totalAmount) > 0.01) {
                $validator->errors()->add(
                    'total_amount',
                    'Total amount calculation is incorrect.'
                );
            }

            // Validate discount amount
            if ($this->filled('discount_amount') && $this->discount_amount > $subTotal) {
                $validator->errors()->add(
                    'discount_amount',
                    'Discount amount cannot be greater than subtotal.'
                );
            }

            // Validate order status transitions
            if ($this->filled('status')) {
                $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];
                if (! in_array($this->status, $validStatuses)) {
                    $validator->errors()->add(
                        'status',
                        'Invalid order status.'
                    );
                }
            }

            // Validate payment status
            if ($this->filled('payment_status')) {
                $validPaymentStatuses = ['pending', 'paid', 'unpaid', 'refunded', 'partially_refunded'];
                if (! in_array($this->payment_status, $validPaymentStatuses)) {
                    $validator->errors()->add(
                        'payment_status',
                        'Invalid payment status.'
                    );
                }
            }
        });
    }
}
