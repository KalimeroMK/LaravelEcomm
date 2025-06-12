<?php

declare(strict_types=1);

namespace Modules\User\Http\Requests;

use Modules\Core\Http\Requests\CoreRequest;

class Store extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'roles' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ];
    }
}
