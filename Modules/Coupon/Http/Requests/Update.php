<?php

namespace Modules\Coupon\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function rules(): array
    {
        return [
            'code'   => 'string|required|unique:coupons',
            'type'   => 'required|in:fixed,percent',
            'value'  => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ];
    }
    
    public function authorize(): bool
    {
        return true;
    }
}
