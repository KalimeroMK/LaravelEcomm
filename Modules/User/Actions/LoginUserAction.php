<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Modules\User\Models\User;

readonly class LoginUserAction
{
    public function execute(string $email, string $password): ?array
    {
        if (! Auth::attempt(['email' => $email, 'password' => $password])) {
            return null;
        }

        /** @var User&Authenticatable $user */
        $user = Auth::user();

        return [
            'token' => $user->createToken('MyAuthApp')->plainTextToken,
            'name' => $user->name,
        ];
    }
}
