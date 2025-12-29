<?php

declare(strict_types=1);

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Core\Support\Relations\SyncRelations;
use Modules\Role\Actions\GetAllRolesAction;
use Modules\User\Actions\DeleteUserAction;
use Modules\User\Actions\FindUserAction;
use Modules\User\Actions\GetAllUsersAction;
use Modules\User\Actions\GetUserRolesAction;
use Modules\User\Actions\StoreUserAction;
use Modules\User\Actions\UpdateUserAction;
use Modules\User\DTOs\UserDTO;
use Modules\User\Http\Requests\Api\Store;
use Modules\User\Http\Requests\Api\Update;
use Modules\User\Http\Resource\UserResource;
use Modules\User\Models\User;
use Modules\User\Repository\UserRepository;

class UserController extends CoreController
{
    public function __construct(
        private readonly GetAllUsersAction $getAllAction,
        private readonly FindUserAction $findAction,
        private readonly StoreUserAction $storeAction,
        private readonly UpdateUserAction $updateAction,
        private readonly DeleteUserAction $deleteAction,
        private readonly GetAllRolesAction $getAllRolesAction,
        private readonly GetUserRolesAction $getUserRolesAction
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', User::class);

        return UserResource::collection($this->getAllAction->execute()->users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', User::class);

        $dto = UserDTO::fromRequest($request);
        $user = $this->storeAction->execute($dto);

        // Sync roles if provided
        if ($request->has('roles')) {
            SyncRelations::execute($user, ['roles' => $request->input('roles')]);
        }

        return $this
            ->setMessage(__('apiResponse.storeSuccess', ['resource' => 'User']))
            ->respond(new UserResource($user->fresh('roles')));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $this->authorizeFromRepo(UserRepository::class, 'view', $id);
        $user = $this->findAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.ok', ['resource' => 'User']))
            ->respond(new UserResource($user->load('roles')));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $existingUser = $this->findAction->execute($id);
        $this->authorize('update', $existingUser);

        $dto = UserDTO::fromRequest($request, $id);
        $user = $this->updateAction->execute($id, $dto);

        // Sync roles if provided
        if ($request->has('roles')) {
            SyncRelations::execute($user, ['roles' => $request->input('roles')]);
        }

        return $this
            ->setMessage(__('apiResponse.updateSuccess', ['resource' => 'User']))
            ->respond(new UserResource($user->fresh('roles')));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->authorizeFromRepo(UserRepository::class, 'delete', $id);
        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', ['resource' => 'User']))
            ->respond(null);
    }
}
