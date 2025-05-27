<?php

declare(strict_types=1);

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Role\Actions\GetAllRolesAction;
use Modules\User\Actions\DeleteUserAction;
use Modules\User\Actions\FindUserAction;
use Modules\User\Actions\GetAllUsersAction;
use Modules\User\Actions\GetUserRolesAction;
use Modules\User\Actions\ProfileUpdateAction;
use Modules\User\Actions\StoreUserAction;
use Modules\User\Actions\UpdateUserAction;
use Modules\User\Http\Requests\Store;
use Modules\User\Http\Requests\Update;
use Modules\User\Models\User;

class UserController extends Controller
{
    private GetAllUsersAction $getAllUsersAction;
    private StoreUserAction $storeUserAction;
    private UpdateUserAction $updateUserAction;
    private DeleteUserAction $deleteUserAction;
    private FindUserAction $findUserAction;
    private GetAllRolesAction $getAllRolesAction;
    private GetUserRolesAction $getUserRolesAction;
    private ProfileUpdateAction $profileUpdateAction;

    public function __construct(
        GetAllUsersAction $getAllUsersAction,
        StoreUserAction $storeUserAction,
        UpdateUserAction $updateUserAction,
        DeleteUserAction $deleteUserAction,
        FindUserAction $findUserAction,
        GetAllRolesAction $getAllRolesAction,
        GetUserRolesAction $getUserRolesAction,
        ProfileUpdateAction $profileUpdateAction
    ) {
        $this->getAllUsersAction = $getAllUsersAction;
        $this->storeUserAction = $storeUserAction;
        $this->updateUserAction = $updateUserAction;
        $this->deleteUserAction = $deleteUserAction;
        $this->findUserAction = $findUserAction;
        $this->getAllRolesAction = $getAllRolesAction;
        $this->getUserRolesAction = $getUserRolesAction;
        $this->profileUpdateAction = $profileUpdateAction;
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        if (Auth::user() && Auth::user()->isSuperAdmin()) {
            $usersDto = $this->getAllUsersAction->execute();
            $users = $usersDto->users;
        } elseif (!is_numeric($userId)) {
            abort(404, 'User not found.');
        } else {
            $userDto = $this->findUserAction->execute((int)$userId);
            $users = [$userDto];
        }

        return view('user::index', ['users' => $users])->with(
            'i',
            ($request->input('page', 1) - 1) * 5
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Store $request): RedirectResponse
    {
        $this->storeUserAction->execute($request->validated());

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        $roles = $this->getAllRolesAction->execute();

        return view('user::create', [
            'user' => [],
            'roles' => $roles,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @return Application|Factory|View
     */
    public function show(User $user)
    {
        $userDto = $this->findUserAction->execute($user->id);

        return view('user::edit', ['user' => $userDto->user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Application|Factory|View
     */
    public function edit(User $user)
    {
        $roles = $this->getAllRolesAction->execute();
        $userRole = $this->getUserRolesAction->execute($user->id);
        $userDto = $this->findUserAction->execute($user->id);

        return view('user::edit', ['user' => $userDto->user, 'roles' => $roles, 'userRole' => $userRole]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Update $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $this->updateUserAction->execute($id, $request->validated());

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->deleteUserAction->execute($user->id);

        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }

    /**
     * @return Application|Factory|View
     */
    public function profile()
    {
        $user = Auth()->user();
        $userDto = $this->findUserAction->execute($user->id);

        return view('user::profile', ['profile' => $userDto->user]);
    }

    /**
     * @return RedirectResponse
     */
    public function impersonate(User $user)
    {
        $authUser = auth()->user();

        if ($authUser === null) {
            return redirect()->back()->withErrors('No authenticated user found.');
        }

        $authUser->impersonate($user);

        return redirect()->route('admin');
    }

    /**
     * @return RedirectResponse
     */
    public function leaveImpersonate()
    {
        $authUser = auth()->user();

        if ($authUser === null) {
            return redirect()->back()->withErrors('No authenticated user found.');
        }

        $authUser->leaveImpersonation();

        return redirect()->route('admin');
    }

    /**
     * @return RedirectResponse
     */
    public function profileUpdate(Request $request, User $user)
    {
        $status = $this->profileUpdateAction->execute($user, $request->all());
        if ($status) {
            request()->session()->flash('success', 'Successfully updated your profile');
        } else {
            request()->session()->flash('error', 'Please try again!');
        }

        return redirect()->back();
    }
}
