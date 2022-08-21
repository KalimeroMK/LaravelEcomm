<?php

namespace Modules\Size\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Update extends CoreRequest
{
    public function rules(): array
    {
        return [
            'name' => 'string|required|unique:sizes',
        ];
    }
    
    public function authorize(): bool
    {
        return true;
    }
}
