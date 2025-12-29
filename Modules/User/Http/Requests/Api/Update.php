<?php

declare(strict_types=1);

namespace Modules\User\Http\Requests\Api;

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
        $userId = $this->route('user') ?? $this->route('id');

        return [
            'name' => 'sometimes|required|string|max:50',
            'email' => 'sometimes|required|email|unique:users,email,'.$userId,
            'password' => 'nullable|string|min:8',
            'roles' => 'sometimes|required|array',
            'roles.*' => 'exists:roles,id',
            'photo' => 'nullable|image|max:2048',
        ];
    }
}
