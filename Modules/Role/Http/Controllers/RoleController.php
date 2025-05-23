<?php

declare(strict_types=1);

namespace Modules\Role\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Permission\Models\Permission;
use Modules\Role\Actions\DeleteRoleAction;
use Modules\Role\Actions\GetAllRolesAction;
use Modules\Role\Actions\StoreRoleAction;
use Modules\Role\Actions\UpdateRoleAction;
use Modules\Role\DTOs\RoleDTO;
use Modules\Role\Models\Role;

class RoleController extends CoreController
{
    /**
     * RoleController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Role::class, 'role');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $rolesDto = (new GetAllRolesAction())->execute();

        return view('role::index', ['roles' => $rolesDto->roles]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $permissions = Permission::all()->toArray();

        return view('role::create', ['permissions' => $permissions]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        (new StoreRoleAction())->execute($request->all());

        return redirect()->route('roles.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role): View
    {
        $permissions = Permission::all()->toArray();
        $roleDto = new RoleDTO($role->load('permissions'));

        return view('role::edit', [
            'role' => (array) $roleDto,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        (new UpdateRoleAction())->execute($role->id, $request->all());

        return redirect()->route('roles.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        (new DeleteRoleAction())->execute($role->id);

        return redirect()->route('roles.index');
    }
}
