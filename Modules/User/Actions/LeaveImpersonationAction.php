<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Exception;

readonly class LeaveImpersonationAction
{
    public function execute(): void
    {
        $authUser = auth()->user();

        if ($authUser === null) {
            throw new Exception('No authenticated user found.');
        }

        $authUser->leaveImpersonation();
    }
}
