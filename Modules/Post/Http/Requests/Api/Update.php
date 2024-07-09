<?php

namespace Modules\Post\Http\Requests\Api;

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
            'title' => 'string|required',
            'quote' => 'string|nullable',
            'summary' => 'string|required',
            'description' => 'string|nullable',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'tags' => 'nullable',
            'added_by' => 'nullable',
            'category' => 'sometimes|array',
            'category.*' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive',
        ];
    }
}
