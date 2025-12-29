<?php

declare(strict_types=1);

namespace Modules\Tenant\Http\Requests\Api;

use Modules\Core\Http\Requests\CoreRequest;

class Store extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\-\.]+$/'],
            'database' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_]+$/'],
        ];
    }
}
