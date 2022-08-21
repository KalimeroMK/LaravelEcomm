<?php

namespace Modules\Banner\Http\Requests\Api;

use JetBrains\PhpStorm\ArrayShape;
use Modules\Core\Http\Requests\Api\CoreRequest;

class Update extends CoreRequest
{
    #[ArrayShape([
        'title'       => "string",
        'description' => "string",
        'photo'       => "string",
        'status'      => "string",
    ])] public function rules(): array
    {
        return [
            'title'       => 'string|required|max:50',
            'description' => 'string|nullable',
            'photo'       => 'sometimes|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'status'      => 'required|in:active,inactive',
        ];
    }
    
    public function authorize(): bool
    {
        return true;
    }
}
