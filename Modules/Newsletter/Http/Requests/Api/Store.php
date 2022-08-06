<?php

namespace Modules\Newsletter\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Store extends CoreRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:newsletters',
        ];
    }
    
    public function authorize(): bool
    {
        return true;
    }
}