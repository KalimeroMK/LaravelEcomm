<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Illuminate\Support\Facades\Hash;
use Modules\User\Models\User;

class RegisterUserAction
{
    public function execute(array $data): array
    {
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        return [
            'token' => $user->createToken('MyAuthApp')->plainTextToken,
            'name' => $user->name,
        ];
    }
}
