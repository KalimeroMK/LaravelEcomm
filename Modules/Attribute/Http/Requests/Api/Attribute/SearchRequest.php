<?php

declare(strict_types=1);

namespace Modules\Attribute\Http\Requests\Api\Attribute;

use Modules\Core\Http\Requests\Api\CoreRequest;

class SearchRequest extends CoreRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
        ];
    }
}
