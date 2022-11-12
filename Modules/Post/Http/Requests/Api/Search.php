<?php

namespace Modules\Post\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Search extends CoreRequest
{
    public function rules(): array
    {
        return [
            'title'        => 'string|nullable',
            'quote'        => 'string|nullable',
            'summary'      => 'string|nullable',
            'description'  => 'string|nullable',
            'status'       => 'string|in:active,inactive|nullable',
            'per_page'     => 'nullable|int',
            'all_included' => 'nullable|boolean',
        ];
    }
    
}