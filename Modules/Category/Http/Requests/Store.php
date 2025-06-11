<?php

declare(strict_types=1);

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
            'title' => 'required|string|unique:categories,title|max:50',
            'statues' => 'sometimes|in:active,inactive',
            'parent_id' => 'sometimes|exists:categories,id|not_in:'.$category,
        ];
    }
}
