<?php

declare(strict_types=1);

namespace Modules\Role\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Core\Traits\ApiResponses;
use Modules\Role\Actions\DeleteRoleAction;
use Modules\Role\Actions\GetAllRolesAction;
use Modules\Role\Actions\StoreRoleAction;
use Modules\Role\Actions\UpdateRoleAction;
use Modules\Role\DTOs\RoleDTO;
use Modules\Role\Http\Requests\Api\Store;
use Modules\Role\Http\Requests\Api\Update;
use Modules\Role\Http\Resources\RoleResource;
use Modules\Role\Models\Role;

class RoleController extends CoreController
{
    use ApiResponses;

    public function __construct()
    {
        $this->middleware('permission:role-list', ['only' => ['index']]);
        $this->middleware('permission:role-show', ['only' => ['show']]);
        $this->middleware('permission:role-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:role-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    public function index(): AnonymousResourceCollection
    {
        $rolesDto = (new GetAllRolesAction())->execute();

        return RoleResource::collection($rolesDto->roles);
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        $roleDto = (new StoreRoleAction())->execute($request->validated());

        return $this->setMessage(__('apiResponse.storeSuccess', ['resource' => 'Role']))->respond(new RoleResource($roleDto));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $role = Role::findOrFail($id);
        $roleDto = new RoleDTO($role->load('permissions'));

        return $this->setMessage(__('apiResponse.ok', ['resource' => 'Role']))->respond(new RoleResource($roleDto));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $roleDto = (new UpdateRoleAction())->execute($id, $request->validated());

        return $this->setMessage(__('apiResponse.updateSuccess', ['resource' => 'Role']))->respond(new RoleResource($roleDto));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        (new DeleteRoleAction())->execute($id);

        return $this->setMessage(__('apiResponse.deleteSuccess', ['resource' => 'Role']))->respond(null);
    }
}
