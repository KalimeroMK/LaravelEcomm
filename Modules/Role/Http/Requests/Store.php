<?php

declare(strict_types=1);

namespace Modules\Role\Http\Requests;

use Modules\Core\Http\Requests\CoreRequest;

class Store extends CoreRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'guard_name' => 'required|string|max:255|in:web,api|default:web|unique:permissions',
        ];
    }
}
