<?php

namespace Modules\Banner\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Update extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string> Array of field rules.
     */
    public function rules(): array
    {
        return [
            'title' => 'string|required|max:50',
            'description' => 'string|nullable',
            'photo' => 'sometimes|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'status' => 'required|in:active,inactive',
        ];
    }
}
