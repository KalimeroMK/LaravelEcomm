<?php

    namespace Modules\User\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;
    use JetBrains\PhpStorm\ArrayShape;

    class LoginRequest extends FormRequest
    {

        public mixed $name;
        public mixed $email;
        public mixed $password;

        #[ArrayShape(
            [
                'name'     => "string",
                'email'    => "string",
                'password' => "string",
            ])
        ]
        public function rules(): array
        {
            return [
                'name'     => 'required|min:4',
                'email'    => 'required|email',
                'password' => 'required|min:8',
            ];
        }

        public function authorize(): bool
        {
            return true;
        }
    }