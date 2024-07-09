<?php

namespace Modules\Brand\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Search extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string> Array of field rules.
     */
    public function rules(): array
    {
        return [
            'title' => 'string|nullable',
            'status' => 'string|nullable',
            'slug' => 'string|nullable',
            'per_page' => 'nullable|int',
            'all_included' => 'nullable|boolean',
        ];
    }
}
