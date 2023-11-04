<?php

namespace Modules\Role\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */

    function __construct()
    {
//        $this->authorizeResource(Role::class, 'role');
    }

    public function index()
    {
        $roles = Role::all();
        return view('role::index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('role::create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permissions'));
        return redirect()->route('role.index');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('role::edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $role->name = $request->input('name');
        $role->syncPermissions($request->input('permissions'));
        $role->save();
        return redirect()->route('role.index');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('role.index');
    }
}
