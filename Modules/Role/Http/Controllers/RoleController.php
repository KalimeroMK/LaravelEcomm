<?php

declare(strict_types=1);

namespace Modules\Role\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Role\Actions\DeleteRoleAction;
use Modules\Role\Actions\FindRoleAction;
use Modules\Role\Actions\GetAllPermissionsAction;
use Modules\Role\Actions\GetAllRolesAction;
use Modules\Role\Actions\StoreRoleAction;
use Modules\Role\Actions\UpdateRoleAction;
use Modules\Role\DTOs\RoleDTO;
use Modules\Role\Http\Requests\Store;
use Modules\Role\Http\Requests\Update;
use Modules\Role\Models\Role;

class RoleController extends CoreController
{
    private readonly GetAllRolesAction $getAllAction;

    private readonly FindRoleAction $findAction;

    private readonly StoreRoleAction $storeAction;

    private readonly UpdateRoleAction $updateAction;

    private readonly DeleteRoleAction $deleteAction;

    private readonly GetAllPermissionsAction $getAllPermissionsAction;

    /**
     * RoleController constructor.
     */
    public function __construct(
        GetAllRolesAction $getAllAction,
        FindRoleAction $findAction,
        StoreRoleAction $storeAction,
        UpdateRoleAction $updateAction,
        DeleteRoleAction $deleteAction,
        GetAllPermissionsAction $getAllPermissionsAction
    ) {
        $this->getAllAction = $getAllAction;
        $this->findAction = $findAction;
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
        $roles = $this->getAllAction->execute();

        return view('role::index', ['roles' => $roles]);
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
    public function store(Store $request): RedirectResponse
    {
        $dto = RoleDTO::fromRequest($request);
        $this->storeAction->execute($dto);

        return redirect()->route('roles.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role): View
    {
        $role = $this->findAction->execute($role->id);
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
    public function update(Update $request, Role $role): RedirectResponse
    {
        $dto = RoleDTO::fromRequest($request, $role->id);
        $this->updateAction->execute($role->id, $dto);

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
