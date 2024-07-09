<?php

namespace Modules\Coupon\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Store extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<string>> Array of validation rules where values can be strings or arrays of strings.
     */
    public function rules(): array
    {
        return [
            'code' => 'string|required|unique:coupons',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ];
    }
}
