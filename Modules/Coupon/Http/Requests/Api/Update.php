<?php

namespace Modules\Coupon\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Update extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<string>>
     */
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
