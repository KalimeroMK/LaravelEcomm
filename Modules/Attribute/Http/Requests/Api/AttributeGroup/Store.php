<?php

declare(strict_types=1);

namespace Modules\Attribute\Http\Requests\Api\AttributeGroup;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Store extends CoreRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:attribute_groups,name',
        ];
    }
}
