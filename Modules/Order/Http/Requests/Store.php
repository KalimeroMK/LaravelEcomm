<?php

namespace Modules\Order\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Order\Rules\CartRule;

class Store extends FormRequest
{
    /**
     * Shipping information.
     *
     * @var string[]
     */
    public array $shipping;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|CartRule>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'string|required',
            'last_name' => 'string|required',
            'address1' => 'string|required',
            'address2' => 'string|nullable',
            'coupon' => 'nullable|numeric',
            'phone' => 'numeric|required',
            'post_code' => 'string|nullable',
            'email' => 'string|required',
            'cart' => new CartRule(), // Ensure this custom rule is properly included
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }
}
