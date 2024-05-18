<?php

namespace Modules\Permission\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the permissions.
     *
     * @return View
     */
    public function index(): View
    {
        $permissions = Permission::all();
        return view('permission::index', compact('permissions'));
    }

    /**
     * Show the form for creating a new permission.
     *
     * @return View
     */
    public function create(): View
    {
        return view('permission::create', ['permission' => new Permission()]);
    }

    /**
     * Store a newly created permission in storage.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        Permission::create(['name' => $request->input('name')]);
        return redirect()->route('permissions.index');
    }

    /**
     * Show the form for editing the specified permission.
     *
     * @param  Permission  $permission
     * @return View
     */
    public function edit(Permission $permission): View
    {
        return view('permission::edit', compact('permission'));
    }

    /**
     * Update the specified permission in storage.
     *
     * @param  Request  $request
     * @param  Permission  $permission
     * @return RedirectResponse
     */
    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $permission->name = $request->input('name');
        $permission->save();
        return redirect()->route('permissions.index');
    }

    /**
     * Remove the specified permission from storage.
     *
     * @param  Permission  $permission
     * @return RedirectResponse
     */
    public function destroy(Permission $permission): RedirectResponse
    {
        $permission->delete();
        return redirect()->route('permissions.index');
    }
}
