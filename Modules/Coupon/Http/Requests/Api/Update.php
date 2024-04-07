<?php

namespace Modules\Coupon\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Update extends CoreRequest
{
    public function rules(): array
    {
        return [
            'code' => 'string|nullable|unique:coupons',
            'type' => 'nullable|in:fixed,percent',
            'value' => 'nullable|numeric',
            'status' => 'nullable|in:active,inactive',
        ];
    }
}
