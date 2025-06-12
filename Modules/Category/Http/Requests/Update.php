<?php

declare(strict_types=1);

namespace Modules\Category\Http\Requests;

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
            'title' => 'required|string|unique:categories,title|max:50',
            'parent_id' => 'sometimes|exists:categories,id|not_in:'.$category,
        ];
    }
}
