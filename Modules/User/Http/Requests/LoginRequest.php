<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public mixed $name;

    public mixed $email;

    public mixed $password;

    public function rules(): array
    {
        return [
            'name' => 'required|min:4',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
