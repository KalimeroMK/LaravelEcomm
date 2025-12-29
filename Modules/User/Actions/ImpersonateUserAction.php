<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Exception;
use Modules\User\Models\User;

readonly class ImpersonateUserAction
{
    public function execute(User $user): void
    {
        $authUser = auth()->user();

        if ($authUser === null) {
            throw new Exception('No authenticated user found.');
        }

        $authUser->impersonate($user);
    }
}
