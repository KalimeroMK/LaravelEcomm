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

        // ImpersonateManager::take() does NOT consult canImpersonate() or
        // canBeImpersonated() - the package only checks those in its own
        // controller, which this application does not use. Enforcing them here
        // is what actually makes the model overrides effective.
        if (! $authUser->canImpersonate()) {
            throw new Exception('You are not allowed to impersonate other users.');
        }

        if (! $user->canBeImpersonated()) {
            throw new Exception('This account cannot be impersonated.');
        }

        $authUser->impersonate($user);
    }
}
