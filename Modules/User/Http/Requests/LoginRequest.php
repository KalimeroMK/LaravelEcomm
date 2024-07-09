<?php

namespace Modules\User\Http\Requests;

use Modules\Core\Http\Requests\CoreRequest;

class LoginRequest extends CoreRequest
{
    public string $name;
    public string $email;
    public string $password;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:4',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ];
    }


}
