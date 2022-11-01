<?php

namespace Modules\Brand\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Store extends CoreRequest
{
    public function rules(): array
    {
        return [
            'title' => 'string|required|unique:brands',
            'photo' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ];
    }
    
    public function authorize(): bool
    {
        return true;
    }
}
