<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Illuminate\Support\Facades\Auth;

class LoginUserAction
{
    public function execute(string $email, string $password): ?array
    {
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = Auth::user();

            return [
                'token' => $user->createToken('MyAuthApp')->plainTextToken,
                'name' => $user->name,
            ];
        }

        return null;
    }
}
