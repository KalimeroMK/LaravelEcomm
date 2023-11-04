<?php

namespace Modules\Permission\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return view('permission::index', compact('permissions'));
    }

    public function create()
    {
        return view('permission::create', ['permission' => new Permission()]);
    }


    public function store(Request $request)
    {
        Permission::create(['name' => $request->input('name')]);
        return redirect()->route('permissions.index');
    }

    public function edit(Permission $permission)
    {
        return view('permission::edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $permission->name = $request->input('name');
        $permission->save();
        return redirect()->route('permissions.index');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index');
    }
}
