<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id = $this->route('users')->id,
            'password' => 'same:confirm-password',
            'roles' => 'required',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
