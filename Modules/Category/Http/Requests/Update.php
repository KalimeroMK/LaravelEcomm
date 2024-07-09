<?php

namespace Modules\Category\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\CoreRequest;

class Update extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<string>>
     */
    public function rules(): array
    {
        $category = optional($this->route('category'))->id;

        return [
            'title' => [
                'nullable',
                'string',
                Rule::unique('categories', 'title')->ignore($category), // Specify the key and rule correctly.
            ],
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                Rule::notIn([$category]), // This rule is set correctly.
            ],
        ];
    }
}
