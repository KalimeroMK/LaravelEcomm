<?php

namespace Modules\Core\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;

class CoreRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
        
        ];
    }
    
    public function authorize(): bool
    {
        return true;
    }
    
    protected function failedValidation(Validator|\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}