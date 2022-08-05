<?php

namespace Modules\Brand\Http\Requests\Api;

use JetBrains\PhpStorm\ArrayShape;
use Modules\Core\Helpers\ApiRequest;
use Modules\Core\Http\Requests\Api\CoreRequest;

class Store extends CoreRequest
{
    #[ArrayShape([
        'title' => "string",
        'photo' => "string",
    ])] public function rules(): array
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
