<?php

namespace Modules\Tenant\Http\Requests;

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
            'name' => ['nullable', 'string'],
            'domain' => ['nullable', 'string'],
            'database' => ['nullable', 'string'],
        ];
    }


}
