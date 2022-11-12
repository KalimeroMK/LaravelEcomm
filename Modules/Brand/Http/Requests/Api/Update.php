<?php

namespace Modules\Brand\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Update extends CoreRequest
{
    public function rules(): array
    {
        return [
            'title' => 'string|required|unique:brands',
        ];
    }
}
