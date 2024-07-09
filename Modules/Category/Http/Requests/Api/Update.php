<?php

namespace Modules\Category\Http\Requests\Api;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\Api\CoreRequest;

class Update extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<string>> Array of validation rules where values can be strings or arrays of strings.
     */
    public function rules(): array
    {
        $category = optional($this->route('category'))->id;

        return [
            'title' => [
                'nullable',
                'string',
                Rule::unique('categories', 'title')->ignore($category),
            ],
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                Rule::notIn([$category]),
            ],
        ];
    }
}
