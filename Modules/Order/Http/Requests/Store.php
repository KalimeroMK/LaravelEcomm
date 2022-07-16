<?php

namespace Modules\Order\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Order\Rules\CartRule;

class Store extends FormRequest
{
    /**
     * @var mixed
     */
    public mixed $shipping;
    
    public function rules(): array
    {
        return [
            'first_name' => 'string|required',
            'last_name'  => 'string|required',
            'address1'   => 'string|required',
            'address2'   => 'string|nullable',
            'coupon'     => 'nullable|numeric',
            'phone'      => 'numeric|required',
            'post_code'  => 'string|nullable',
            'email'      => 'string|required',
            new CartRule(),
        ];
    }
    
    public function authorize(): bool
    {
        return true;
    }
}
