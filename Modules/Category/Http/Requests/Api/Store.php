<?php

namespace Modules\Category\Http\Requests\Api;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\Api\CoreRequest;

class Store extends CoreRequest
{
    public mixed $title;

    public mixed $parent_id;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<string>> Array of validation rules where values can be strings or arrays of strings.
     */
    public function rules(): array
    {
        $categoryId = optional($this->route('category'))->id;

        return [
            'title' => 'string|required|unique:categories',
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                Rule::notIn([$categoryId])
            ],
        ];
    }
}
