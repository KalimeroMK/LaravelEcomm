<?php

namespace App\Requests;

use Modules\Core\Http\Requests\Api\CoreRequest;

class AuthRequest extends CoreRequest
{
    public function rules(): array
    {
        return [
            'name'             => 'required',
            'email'            => 'required|email|unique:users',
            'password'         => 'required',
            'confirm_password' => 'required|same:password',
        ];
    }
}
