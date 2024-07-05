<?php

namespace Modules\Brand\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'title' => 'string|nullable',
            'status' => 'string|nullable',
            'slug' => 'string|nullable',
            'per_page' => 'nullable|int',
            'all_included' => 'nullable|boolean',

        ];
    }
}
