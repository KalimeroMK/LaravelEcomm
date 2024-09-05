<?php

namespace Modules\Permission\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Permission\Http\Requests\Store;
use Modules\Permission\Http\Requests\Update;
use Modules\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Permission::class, 'permission');
    }

    /**
     * Display a listing of the permissions.
     */
    public function index(): View
    {
        $permissions = Permission::all();

        return view('permission::index', compact('permissions'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create(): View
    {
        return view('permission::create', ['permission' => new Permission]);
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Store $request): RedirectResponse
    {
        Permission::create($request->validated());

        return redirect()->route('permissions.index');
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission): View
    {
        return view('permission::edit', compact('permission'));
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(Update $request, Permission $permission): RedirectResponse
    {
        $permission->update($request->validated());

        return redirect()->route('permissions.index');
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(Permission $permission): RedirectResponse
    {
        $permission->delete();

        return redirect()->route('permissions.index');
    }
}
