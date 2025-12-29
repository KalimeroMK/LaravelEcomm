<?php

declare(strict_types=1);

namespace Modules\Permission\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Permission\Actions\CreatePermissionAction;
use Modules\Permission\Actions\DeletePermissionAction;
use Modules\Permission\Actions\FindPermissionAction;
use Modules\Permission\Actions\GetAllPermissionsAction;
use Modules\Permission\Actions\UpdatePermissionAction;
use Modules\Permission\DTOs\PermissionDTO;
use Modules\Permission\Http\Requests\Store;
use Modules\Permission\Http\Requests\Update;
use Modules\Permission\Http\Resources\PermissionResource;
use Modules\Permission\Models\Permission;
use ReflectionException;

class PermissionController extends CoreController
{
    public function __construct(
        private readonly GetAllPermissionsAction $getAllAction,
        private readonly FindPermissionAction $findAction,
        private readonly CreatePermissionAction $createAction,
        private readonly UpdatePermissionAction $updateAction,
        private readonly DeletePermissionAction $deleteAction,
    ) {}

    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Permission::class);

        $permissionsDto = $this->getAllAction->execute();

        return PermissionResource::collection($permissionsDto->permissions);
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', Permission::class);

        $dto = PermissionDTO::fromRequest($request);
        $permission = $this->createAction->execute($dto);

        return $this
            ->setMessage(__('apiResponse.storeSuccess', ['resource' => Helper::getResourceName(Permission::class)]))
            ->respond(new PermissionResource($permission));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $permission = $this->findAction->execute($id);
        $this->authorize('view', $permission);

        return $this
            ->setMessage(__('apiResponse.ok', ['resource' => Helper::getResourceName(Permission::class)]))
            ->respond(new PermissionResource($permission));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $permission = $this->findAction->execute($id);
        $this->authorize('update', $permission);

        $dto = PermissionDTO::fromRequest($request, $id, $permission);
        $permission = $this->updateAction->execute($dto);

        return $this
            ->setMessage(__('apiResponse.updateSuccess', ['resource' => Helper::getResourceName(Permission::class)]))
            ->respond(new PermissionResource($permission));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $permission = $this->findAction->execute($id);
        $this->authorize('delete', $permission);

        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', ['resource' => Helper::getResourceName(Permission::class)]))
            ->respond(null);
    }
}
