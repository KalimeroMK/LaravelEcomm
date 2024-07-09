<?php

namespace Modules\Coupon\Http\Requests;

use Modules\Core\Http\Requests\CoreRequest;

class Update extends CoreRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'code' => 'string|required|unique:coupons,code,'.$this->coupon->id,
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ];
    }
}
