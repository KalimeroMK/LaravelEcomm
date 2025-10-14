<?php

declare(strict_types=1);

namespace Modules\Attribute\Http\Requests\Api\AttributeGroup;

use Exception;
use Modules\Core\Http\Requests\Api\CoreRequest;

class Update extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:attribute_groups,name',
        ];
    }
}
