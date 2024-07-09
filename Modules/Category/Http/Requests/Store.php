<?php

namespace Modules\Category\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\CoreRequest;

class Store extends CoreRequest
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
            'title' => 'string|required|unique:categories,title',
            'status' => 'boolean|required',
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                Rule::notIn([$category]), // Prevents setting the category itself as its parent.
            ],
        ];
    }
}
