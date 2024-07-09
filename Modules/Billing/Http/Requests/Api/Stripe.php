<?php

namespace Modules\Billing\Http\Requests\Api;

use Modules\Billing\Rules\CartExistRule;
use Modules\Core\Http\Requests\Api\CoreRequest;

class Stripe extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|CartExistRule>
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
            'shipping' => 'string|nullable',
            'payment_method' => 'string|nullable',
            'status' => 'string|nullable',
            'cart' => new CartExistRule(),  // Assign a key to the custom rule for clarity
        ];
    }
}
