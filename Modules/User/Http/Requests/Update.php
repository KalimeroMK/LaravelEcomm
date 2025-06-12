<?php

declare(strict_types=1);

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:50',
            'email' => 'sometimes|required|email|unique:users,email',
            'password' => 'nullable|string|min:8',
            'roles' => 'sometimes|required|string|max:255',
            'confirm-password' => 'sometimes|required|string|min:8',
            'photo' => 'nullable|image|max:2048',
        ];
    }
}
