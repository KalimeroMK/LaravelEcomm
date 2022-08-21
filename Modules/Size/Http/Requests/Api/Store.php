<?php

namespace Modules\Size\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Store extends CoreRequest
{
    public function rules(): array
    {
        return [
            'title'  => 'string|required|unique:tags',
            'status' => 'required|in:active,inactive',
        
        ];
    }
    
    public function authorize(): bool
    {
        return true;
    }
}
