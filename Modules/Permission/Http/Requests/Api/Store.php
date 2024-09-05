<?php

namespace Modules\Permission\Http\Requests\Api;

use Modules\Core\Http\Requests\CoreRequest;

class Store extends CoreRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:permissions',
            'guard_name' => 'required|string|max:255|in:web,api|default:web|unique:permissions',
        ];
    }
}
