<?php

namespace Modules\Category\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Store extends CoreRequest
{
    public mixed $title;

    public mixed $parent_id;

    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'title' => 'string|required|unique:categories',
            'parent_id' => 'nullable|exists:categories,id',
        ];
    }
}
