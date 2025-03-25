<?php

declare(strict_types=1);

namespace Modules\Banner\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Search extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'status' => 'string|in:active,inactive|nullable',
            'per_page' => 'nullable|int',
            'all_included' => 'nullable|boolean',
        ];
    }
}
