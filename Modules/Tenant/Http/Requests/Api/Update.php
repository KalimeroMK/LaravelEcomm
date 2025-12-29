<?php

declare(strict_types=1);

namespace Modules\Tenant\Http\Requests\Api;

use Modules\Core\Http\Requests\CoreRequest;

class Update extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'domain' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\-\.]+$/'],
            'database' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_]+$/'],
        ];
    }
}
