<?php

namespace Modules\Core\Http\Requests;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Update extends CoreRequest
{
    public function rules(): array
    {
        return [
            'short_des' => 'required|string',
            'description' => 'required|string',
            'logo' => 'required',
            'address' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
        ];
    }
}
