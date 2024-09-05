<?php

namespace Modules\Permission\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Modules\Core\Helpers\Helper;
use Modules\Core\Traits\ApiResponses;
use Modules\Permission\Http\Requests\Api\Search;
use Modules\Permission\Http\Resources\PermissionResource;
use Modules\Permission\Service\PermissionService;
use Modules\Role\Http\Requests\Update;
use ReflectionException;

class PermissionController extends Controller
{
    use ApiResponses;

    public PermissionService $permission_service;

    public function __construct(PermissionService $permission_service)
    {
        $this->permission_service = $permission_service;
        $this->middleware('permission:permission-list', ['only' => ['index']]);
        $this->middleware('permission:permission-show', ['only' => ['show']]);
        $this->middleware('permission:permission-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:permission-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:permission-delete', ['only' => ['destroy']]);
    }

    public function index(Search $request): AnonymousResourceCollection
    {
        return PermissionResource::collection($this->permission_service->search($request->validated()));
    }

    /**
     * @throws ReflectionException
     */
    public function store(Request $request): JsonResponse
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.storeSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->permission_service->permission_repository->model
                        ),
                    ]
                )
            )
            ->respond(new PermissionResource($this->permission_service->create($request->validated())));
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
                            $this->permission_service->permission_repository->model
                        ),
                    ]
                )
            )
            ->respond(new PermissionResource($this->permission_service->findById($id)));
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
                            $this->permission_service->permission_repository->model
                        ),
                    ]
                )
            )
            ->respond(new PermissionResource($this->permission_service->update($id, $request->validated())));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->permission_service->delete($id);

        return $this
            ->setMessage(
                __(
                    'apiResponse.deleteSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->permission_service->permission_repository->model
                        ),
                    ]
                )
            )
            ->respond(null);
    }
}
