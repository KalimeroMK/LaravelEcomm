<?php

namespace Modules\Page\Http\Requests;

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
            'title' => ['required'],
            'slug' => ['required'],
            'content' => ['required'],
            'is_active' => ['boolean'],
            'user_id' => ['required', 'exists:users'],
        ];
    }
}
