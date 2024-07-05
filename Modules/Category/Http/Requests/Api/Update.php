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
            'title' => 'string|unique:categories,title,'.$this->route('category'),
        ];
    }
}
