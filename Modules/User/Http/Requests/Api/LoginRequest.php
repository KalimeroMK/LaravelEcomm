<?php

namespace Modules\User\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email'    => 'required|string',
            'password' => 'required|string',
        ];
    }
    
    public function authorize(): bool
    {
        return true;
    }
}