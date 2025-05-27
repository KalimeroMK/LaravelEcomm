<?php

declare(strict_types=1);

namespace Modules\Role\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Core\Traits\ApiResponses;
use Modules\Role\Actions\DeleteRoleAction;
use Modules\Role\Actions\GetAllPermissionsAction;
use Modules\Role\Actions\GetAllRolesAction;
use Modules\Role\Actions\StoreRoleAction;
use Modules\Role\Actions\UpdateRoleAction;
use Modules\Role\Http\Requests\Api\Store;
use Modules\Role\Http\Requests\Api\Update;
use Modules\Role\Http\Resources\RoleResource;
use Modules\Role\Models\Role;
use Modules\Role\Repository\RoleRepository;

class RoleController extends CoreController
{
    use ApiResponses;

    private readonly GetAllRolesAction $getAllAction;

    private readonly StoreRoleAction $storeAction;

    private readonly UpdateRoleAction $updateAction;

    private readonly DeleteRoleAction $deleteAction;

    private readonly GetAllPermissionsAction $getAllPermissionsAction;

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
    }

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Role::class);
        $rolesDto = $this->getAllAction->execute();

        return RoleResource::collection($rolesDto->roles);
    }

    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', Role::class);
        $roleDto = $this->storeAction->execute($request->validated());

        return $this->setMessage(__('apiResponse.storeSuccess',
            ['resource' => 'Role']))->respond(new RoleResource($roleDto));
    }

    public function show(int $id): JsonResponse
    {
        $role = $this->authorizeFromRepo(RoleRepository::class, 'view', $id);

        return $this->setMessage(__('apiResponse.ok', ['resource' => 'Role']))->respond(new RoleResource($role));
    }

    public function update(Update $request, int $id): JsonResponse
    {
        $this->authorizeFromRepo(RoleRepository::class, 'update', $id);
        $roleDto = $this->updateAction->execute($id, $request->validated());

        return $this->setMessage(__('apiResponse.updateSuccess',
            ['resource' => 'Role']))->respond(new RoleResource($roleDto));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->authorizeFromRepo(RoleRepository::class, 'delete', $id);
        $this->deleteAction->execute($id);

        return $this->setMessage(__('apiResponse.deleteSuccess', ['resource' => 'Role']))->respond(null);
    }
}
