<?php

namespace Modules\Brand\Http\Requests\Api;

use JetBrains\PhpStorm\ArrayShape;
use Modules\Core\Helpers\ApiRequest;

class UpdateRequest extends ApiRequest
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
