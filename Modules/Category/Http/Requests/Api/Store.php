<?php

namespace Modules\Category\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Store extends CoreRequest
{
    /**
     * @var mixed
     */
    public mixed $title;
    /**
     * @var mixed
     */
    public mixed $parent_id;
    
    public function rules(): array
    {
        return [
            'title'     => 'string|required|unique:categories',
            'parent_id' => 'nullable|exists:categories,id',
        ];
    }
    
    public function authorize(): bool
    {
        return true;
    }
}
