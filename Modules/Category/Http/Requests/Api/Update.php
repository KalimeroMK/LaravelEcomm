<?php

namespace Modules\Category\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Update extends CoreRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'title' => 'string|nullable',
            'parent_id' => 'nullable|exists:categories,id',
        ];
    }
}
