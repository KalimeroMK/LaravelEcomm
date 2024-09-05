<?php

namespace Modules\Role\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Core\Traits\ApiResponses;
use Modules\Role\Http\Requests\Api\Store;
use Modules\Role\Http\Requests\Api\Update;
use Modules\Role\Http\Resources\RoleResource;
use Modules\Role\Service\RoleService;
use ReflectionException;

class RoleController extends CoreController
{
    use ApiResponses;

    public RoleService $role_service;

    public function __construct(RoleService $role_service)
    {
        $this->role_service = $role_service;
        $this->middleware('permission:role-list', ['only' => ['index']]);
        $this->middleware('permission:role-show', ['only' => ['show']]);
        $this->middleware('permission:role-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:role-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    public function index(): AnonymousResourceCollection
    {
        return RoleResource::collection($this->role_service->getAll());
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.storeSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->role_service->role_repository->model
                        ),
                    ]
                )
            )
            ->respond(new RoleResource($this->role_service->create($request->validated())));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.ok',
                    [
                        'resource' => Helper::getResourceName(
                            $this->role_service->role_repository->model
                        ),
                    ]
                )
            )
            ->respond(new RoleResource($this->role_service->findById($id)));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.updateSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->role_service->role_repository->model
                        ),
                    ]
                )
            )
            ->respond(new RoleResource($this->role_service->update($id, $request->validated())));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->role_service->delete($id);

        return $this
            ->setMessage(
                __(
                    'apiResponse.deleteSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->role_service->role_repository->model
                        ),
                    ]
                )
            )
            ->respond(null);
    }
}
