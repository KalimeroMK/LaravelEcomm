<?php

namespace Modules\Tag\Http\Requests;

use Modules\Core\Http\Requests\CoreRequest;

class Store extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'title' => 'string|required|unique:tags',
            'status' => 'required|in:active,inactive',
        ];
    }
}
