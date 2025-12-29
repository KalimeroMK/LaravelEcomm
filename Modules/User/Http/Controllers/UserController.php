<?php

declare(strict_types=1);

namespace Modules\User\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Role\Actions\GetAllRolesAction;
use Modules\User\Actions\DeleteUserAction;
use Modules\User\Actions\FindUserAction;
use Modules\User\Actions\GetUserRolesAction;
use Modules\User\Actions\GetUsersForIndexAction;
use Modules\User\Actions\ImpersonateUserAction;
use Modules\User\Actions\LeaveImpersonationAction;
use Modules\User\Actions\ProfileUpdateAction;
use Modules\User\Actions\StoreUserAction;
use Modules\User\Actions\UpdateUserAction;
use Modules\User\DTOs\UserDTO;
use Modules\User\Http\Requests\Store;
use Modules\User\Http\Requests\Update;
use Modules\User\Models\User;

class UserController extends CoreController
{
    public function __construct(
        private readonly GetUsersForIndexAction $getUsersForIndexAction,
        private readonly GetAllRolesAction $getAllRolesAction,
        private readonly GetUserRolesAction $getUserRolesAction,
        private readonly FindUserAction $findUserAction,
        private readonly StoreUserAction $storeUserAction,
        private readonly UpdateUserAction $updateUserAction,
        private readonly DeleteUserAction $deleteUserAction,
        private readonly ProfileUpdateAction $profileUpdateAction,
        private readonly ImpersonateUserAction $impersonateUserAction,
        private readonly LeaveImpersonationAction $leaveImpersonationAction
    ) {
        $this->authorizeResource(User::class, 'user');
    }

    public function index(Request $request): View|Factory|Application
    {
        $users = $this->getUsersForIndexAction->execute();

        return view('user::index', ['users' => $users])->with(
            'i',
            ($request->input('page', 1) - 1) * 5
        );
    }

    public function store(Store $request): RedirectResponse
    {
        $dto = UserDTO::fromRequest($request);
        $this->storeUserAction->execute($dto);

        return redirect()->route('users.index')->with('success', __('messages.user_created_successfully'));
    }

    public function create(): Factory|View
    {
        $roles = $this->getAllRolesAction->execute();

        return view('user::create', [
            'user' => [],
            'roles' => $roles,
        ]);
    }

    public function show(User $user): Factory|View
    {
        $userDto = $this->findUserAction->execute($user->id);

        return view('user::edit', ['user' => $userDto->user]);
    }

    public function edit(User $user): Factory|View
    {
        $roles = $this->getAllRolesAction->execute();
        $userRole = $this->getUserRolesAction->execute($user->id);
        $user = $this->findUserAction->execute($user->id);

        return view('user::edit', ['user' => $user, 'roles' => $roles, 'userRole' => $userRole]);
    }

    public function update(Update $request, int $id): RedirectResponse
    {
        $dto = UserDTO::fromRequest($request, $id);
        $this->updateUserAction->execute($id, $dto);

        return redirect()->route('users.index')->with('success', __('messages.user_updated_successfully'));
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->deleteUserAction->execute($user->id);

        return redirect()->route('users.index')->with('success', __('messages.user_deleted_successfully'));
    }

    public function profile(): Factory|View
    {
        $user = auth()->user();
        $userDto = $this->findUserAction->execute($user->id);

        return view('user::profile', ['profile' => $userDto]);
    }

    public function impersonate(User $user): RedirectResponse
    {
        try {
            $this->impersonateUserAction->execute($user);

            return redirect()->route('admin');
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function leaveImpersonate(): RedirectResponse
    {
        try {
            $this->leaveImpersonationAction->execute();

            return redirect()->route('admin');
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function profileUpdate(Request $request, User $user): RedirectResponse
    {
        $dto = UserDTO::fromRequest($request, $user->id);
        $status = $this->profileUpdateAction->execute($user, $dto);

        if ($status) {
            request()->session()->flash('success', __('messages.profile_updated_successfully'));
        } else {
            request()->session()->flash('error', __('messages.please_try_again'));
        }

        return redirect()->back();
    }
}
