<?php

    namespace App\Requests;

    use App\Helpers\ApiRequest;
    use JetBrains\PhpStorm\ArrayShape;

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
