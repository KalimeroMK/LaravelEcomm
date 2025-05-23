<?php

declare(strict_types=1);

namespace Modules\Permission\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\ApiResponses;
use Modules\Permission\Actions\CreatePermissionAction;
use Modules\Permission\Actions\DeletePermissionAction;
use Modules\Permission\Actions\GetAllPermissionsAction;
use Modules\Permission\Actions\UpdatePermissionAction;
use Modules\Permission\DTOs\PermissionDTO;
use Modules\Permission\Http\Requests\Api\Search;
use Modules\Permission\Http\Requests\Update;
use Modules\Permission\Http\Resources\PermissionResource;
use Modules\Permission\Models\Permission;

class PermissionController extends Controller
{
    use ApiResponses;

    public function __construct()
    {
        $this->middleware('permission:permission-list', ['only' => ['index']]);
        $this->middleware('permission:permission-show', ['only' => ['show']]);
        $this->middleware('permission:permission-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:permission-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:permission-delete', ['only' => ['destroy']]);
    }

    public function index(Search $request): AnonymousResourceCollection
    {
        $permissionsDto = (new GetAllPermissionsAction())->execute();

        return PermissionResource::collection($permissionsDto->permissions);
    }

    public function store(Request $request): JsonResponse
    {
        $permissionDto = (new CreatePermissionAction())->execute($request->validated());

        return $this->setMessage(__('apiResponse.storeSuccess', ['resource' => 'Permission']))->respond(new PermissionResource($permissionDto));
    }

    public function show(int $id): JsonResponse
    {
        $permission = Permission::findOrFail($id);
        $permissionDto = new PermissionDTO($permission);

        return $this->setMessage(__('apiResponse.ok', ['resource' => 'Permission']))->respond(new PermissionResource($permissionDto));
    }

    public function update(Update $request, int $id): JsonResponse
    {
        $permissionDto = (new UpdatePermissionAction())->execute($id, $request->validated());

        return $this->setMessage(__('apiResponse.updateSuccess', ['resource' => 'Permission']))->respond(new PermissionResource($permissionDto));
    }

    public function destroy(int $id): JsonResponse
    {
        (new DeletePermissionAction())->execute($id);

        return $this->setMessage(__('apiResponse.deleteSuccess', ['resource' => 'Permission']))->respond(null);
    }
}
