<?php

namespace Modules\Brand\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Search extends CoreRequest
{
    /**
     * @return string[]
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