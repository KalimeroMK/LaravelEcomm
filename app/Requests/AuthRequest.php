<?php

namespace App\Requests;

use JetBrains\PhpStorm\ArrayShape;
use Modules\Core\Helpers\ApiRequest;

class AuthRequest extends ApiRequest
{
    #[ArrayShape([
        'name'             => "string",
        'email'            => "string",
        'password'         => "string",
        'confirm_password' => "string",
    ])] public function rules(): array
    {
        return [
            'name'             => 'required',
            'email'            => 'required|email|unique:users',
            'password'         => 'required',
            'confirm_password' => 'required|same:password',
        ];
    }
}
