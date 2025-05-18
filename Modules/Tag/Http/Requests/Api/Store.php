<?php

declare(strict_types=1);

namespace Modules\Tag\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Store extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<string>>
     */
    public function rules(): array
    {
        return [
            'title' => 'string|required|unique:tags,title',
            'slug' => 'required|unique:tags,slug',
        ];
    }
}
