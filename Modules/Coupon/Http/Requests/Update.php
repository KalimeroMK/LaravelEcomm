<?php

declare(strict_types=1);

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
            'code' => 'required|string|unique:coupons,code|max:50',
            'type' => 'required|in:fixed,percent|string',
            'value' => 'required|numeric|string',
            'status' => 'required|in:active,inactive|string',
        ];
    }
}
