<?php

namespace Modules\Brand\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Store extends CoreRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'title' => 'string|required|unique:brands',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
