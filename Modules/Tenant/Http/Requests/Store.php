<?php

namespace Modules\Tenant\Http\Requests;

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
            'name' => ['required', 'string'],
            'domain' => ['required', 'string'],
            'database' => ['required', 'string'],
        ];
    }

}
