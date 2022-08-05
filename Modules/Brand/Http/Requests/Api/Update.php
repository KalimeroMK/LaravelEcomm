<?php

namespace Modules\Brand\Http\Requests\Api;

use JetBrains\PhpStorm\ArrayShape;
use Modules\Core\Http\Requests\Api\CoreRequest;

class Update extends CoreRequest
{
    #[ArrayShape([
        'title' => "string",
    ])] public function rules(): array
    {
        return [
            'title' => 'string|required|unique:brands',
        ];
    }
    
    public function authorize(): bool
    {
        return true;
    }
}
