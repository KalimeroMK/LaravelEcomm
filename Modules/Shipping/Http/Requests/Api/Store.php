<?php

namespace Modules\Shipping\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Store extends CoreRequest
{
    public function rules(): array
    {
        return [
            'type' => 'string|required|unique:shipping',
            'price' => 'nullable|numeric',
            'status' => 'required|in:active,inactive',
        ];
    }
}
