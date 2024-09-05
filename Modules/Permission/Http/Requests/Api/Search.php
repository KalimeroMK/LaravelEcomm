<?php

namespace Modules\Permission\Http\Requests\Api;

use Modules\Core\Http\Requests\CoreRequest;

class Search extends CoreRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'guard_name' => 'nullable|in:web,api|default:web',
            'per_page' => 'nullable|integer|between:1,100',
        ];
    }
}
