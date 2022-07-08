<?php

namespace Modules\Product\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            //
        ];
    }
    
    public function authorize(): bool
    {
        return true;
    }
}
