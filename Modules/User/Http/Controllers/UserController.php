<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\User\Http\Requests\Store;
use Modules\User\Http\Requests\Update;
use Modules\User\Models\User;
use Modules\User\Service\UserService;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private UserService $user_service;

    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        if (Auth::user()->isSuperAdmin()) {
            $users = $this->user_service->getAll();
        } else {
            $users = $this->user_service->findById(Auth::id());
        }

        return view('user::index', ['users' => $users])->with('i',
            ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Store $request): RedirectResponse
    {
        $this->user_service->create($request->validated());

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('user::create', compact([
            'user' => new User(),
            'roles' => Role::all(),
        ]));
    }

    /**
     * Display the specified resource.
     *
     * @return Application|Factory|View
     */
    public function show(User $user)
    {
        return view('user::edit', ['user' => $this->user_service->findById($user->id)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Application|Factory|View
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRole = User::find($user->id)->roles->pluck('name', 'name')->all();

        return view('user::edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Update $request, int $id): RedirectResponse
    {
        $this->user_service->update($id, $request->validated());

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->user_service->delete($user->id);

        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }

    /**
     * @return Application|Factory|View
     */
    public function profile()
    {
        return view('user::profile', ['profile' => Auth()->user()]);
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
        $status = $user->fill($request->all())->save();
        if ($status) {
            request()->session()->flash('success', 'Successfully updated your profile');
        } else {
            request()->session()->flash('error', 'Please try again!');
        }

        return redirect()->back();
    }
}
