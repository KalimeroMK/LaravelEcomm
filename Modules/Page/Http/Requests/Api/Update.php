<?php

namespace Modules\Page\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

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
            'title' => ['nullable', 'string'],
            'slug' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'user_id' => ['nullable', 'exists:users'],
        ];
    }
}
