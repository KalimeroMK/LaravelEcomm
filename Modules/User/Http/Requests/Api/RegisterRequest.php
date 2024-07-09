<?php

namespace Modules\User\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class RegisterRequest extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
        ];
    }


}
