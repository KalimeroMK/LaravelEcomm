<?php

declare(strict_types=1);

namespace Modules\Permission\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Permission\Actions\CreatePermissionAction;
use Modules\Permission\Actions\DeletePermissionAction;
use Modules\Permission\Actions\GetAllPermissionsAction;
use Modules\Permission\Actions\UpdatePermissionAction;
use Modules\Permission\DTOs\PermissionDTO;
use Modules\Permission\Http\Requests\Store;
use Modules\Permission\Http\Requests\Update;
use Modules\Permission\Models\Permission;

class PermissionController extends Controller
{
    private readonly GetAllPermissionsAction $getAllAction;
    private readonly CreatePermissionAction $createAction;
    private readonly UpdatePermissionAction $updateAction;
    private readonly DeletePermissionAction $deleteAction;

    public function __construct(
        GetAllPermissionsAction $getAllAction,
        CreatePermissionAction $createAction,
        UpdatePermissionAction $updateAction,
        DeletePermissionAction $deleteAction
    ) {
        $this->getAllAction = $getAllAction;
        $this->createAction = $createAction;
        $this->updateAction = $updateAction;
        $this->deleteAction = $deleteAction;
        $this->authorizeResource(Permission::class, 'permission');
    }

    /**
     * Display a listing of the permissions.
     */
    public function index(): View
    {
        $permissionsDto = $this->getAllAction->execute();
        return view('permission::index', ['permissions' => $permissionsDto->permissions]);
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
        $dto = PermissionDTO::fromRequest($request);
        $this->createAction->execute($dto);
        return redirect()->route('permissions.index');
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission): View
    {
        return view('permission::edit', ['permission' => $permission]);
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(Update $request, Permission $permission): RedirectResponse
    {
        $dto = PermissionDTO::fromRequest($request, $permission->id);
        $this->updateAction->execute($dto);
        return redirect()->route('permissions.index');
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(Permission $permission): RedirectResponse
    {
        $this->deleteAction->execute($permission->id);
        return redirect()->route('permissions.index');
    }
}
