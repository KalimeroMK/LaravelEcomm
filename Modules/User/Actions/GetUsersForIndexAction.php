<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Illuminate\Support\Facades\Auth;

readonly class GetUsersForIndexAction
{
    public function __construct(
        private GetAllUsersAction $getAllUsersAction,
        private FindUserAction $findUserAction
    ) {}

    public function execute(): array
    {
        $userId = Auth::id();

        if (Auth::user() && Auth::user()->isSuperAdmin()) {
            $usersDto = $this->getAllUsersAction->execute();

            return $usersDto->users;
        }
        if (! is_numeric($userId)) {
            abort(404, 'User not found.');
        } else {
            $userDto = $this->findUserAction->execute((int) $userId);

            return [$userDto];
        }
    }
}
