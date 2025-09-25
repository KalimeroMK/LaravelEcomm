<?php

declare(strict_types=1);

namespace Modules\User\Http\Requests;

use Modules\Core\Http\Requests\Api\CoreRequest;

class AuthRequest extends CoreRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
        ];
    }
}
