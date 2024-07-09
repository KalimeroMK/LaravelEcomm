<?php

namespace Modules\Post\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class SearchRequest extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<string>>
     */
    public function rules(): array
    {
        return [
            'title' => 'string|nullable',
            'quote' => 'string|nullable',
            'summary' => 'string|nullable',
            'description' => 'string|nullable',
            'status' => 'string|in:active,inactive|nullable',
            'per_page' => 'nullable|int',
            'all_included' => 'nullable|boolean',
        ];
    }
}
