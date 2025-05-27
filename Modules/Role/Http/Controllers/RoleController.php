<?php

declare(strict_types=1);

namespace Modules\Role\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Role\Actions\DeleteRoleAction;
use Modules\Role\Actions\GetAllPermissionsAction;
use Modules\Role\Actions\GetAllRolesAction;
use Modules\Role\Actions\StoreRoleAction;
use Modules\Role\Actions\UpdateRoleAction;
use Modules\Role\DTOs\RoleDTO;
use Modules\Role\Models\Role;

class RoleController extends CoreController
{
    private readonly GetAllRolesAction $getAllAction;
    private readonly StoreRoleAction $storeAction;
    private readonly UpdateRoleAction $updateAction;
    private readonly DeleteRoleAction $deleteAction;
    private readonly GetAllPermissionsAction $getAllPermissionsAction;

    /**
     * RoleController constructor.
     */
    public function __construct(
        GetAllRolesAction $getAllAction,
        StoreRoleAction $storeAction,
        UpdateRoleAction $updateAction,
        DeleteRoleAction $deleteAction,
        GetAllPermissionsAction $getAllPermissionsAction
    ) {
        $this->getAllAction = $getAllAction;
        $this->storeAction = $storeAction;
        $this->updateAction = $updateAction;
        $this->deleteAction = $deleteAction;
        $this->getAllPermissionsAction = $getAllPermissionsAction;
        $this->authorizeResource(Role::class, 'role');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $rolesDto = $this->getAllAction->execute();

        return view('role::index', ['roles' => $rolesDto->roles]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $permissions = $this->getAllPermissionsAction->execute()->toArray();

        return view('role::create', ['permissions' => $permissions]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->storeAction->execute($request->all());

        return redirect()->route('roles.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role): View
    {
        $permissions = $this->getAllPermissionsAction->execute()->toArray();
        $roleDto = RoleDTO::fromArray($role->toArray());

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
        $this->updateAction->execute($role->id, $request->all());

        return redirect()->route('roles.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        $this->deleteAction->execute($role->id);

        return redirect()->route('roles.index');
    }
}
