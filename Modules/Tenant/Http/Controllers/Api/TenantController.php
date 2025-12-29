<?php

declare(strict_types=1);

namespace Modules\Tenant\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Tenant\Actions\CreateTenantAction;
use Modules\Tenant\Actions\DeleteTenantAction;
use Modules\Tenant\Actions\FindTenantAction;
use Modules\Tenant\Actions\GetAllTenantsAction;
use Modules\Tenant\Actions\UpdateTenantAction;
use Modules\Tenant\DTOs\TenantDTO;
use Modules\Tenant\Http\Requests\Api\Store;
use Modules\Tenant\Http\Requests\Api\Update;
use Modules\Tenant\Http\Resources\TenantResource;
use Modules\Tenant\Models\Tenant;
use Modules\Tenant\Repository\TenantRepository;

class TenantController extends CoreController
{
    public function __construct(
        private readonly GetAllTenantsAction $getAllAction,
        private readonly FindTenantAction $findAction,
        private readonly CreateTenantAction $createAction,
        private readonly UpdateTenantAction $updateAction,
        private readonly DeleteTenantAction $deleteAction
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Tenant::class);

        return TenantResource::collection($this->getAllAction->execute());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', Tenant::class);

        $dto = TenantDTO::fromRequest($request);
        $tenant = $this->createAction->execute($dto);

        return $this
            ->setMessage(__('apiResponse.storeSuccess', ['resource' => 'Tenant']))
            ->respond(new TenantResource($tenant));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $this->authorizeFromRepo(TenantRepository::class, 'view', $id);
        $tenant = $this->findAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.ok', ['resource' => 'Tenant']))
            ->respond(new TenantResource($tenant));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $existingTenant = $this->authorizeFromRepo(TenantRepository::class, 'update', $id);
        $existingTenant = $this->findAction->execute($id);

        $dto = TenantDTO::fromRequest($request, $id, $existingTenant);
        $tenant = $this->updateAction->execute($id, $dto);

        return $this
            ->setMessage(__('apiResponse.updateSuccess', ['resource' => 'Tenant']))
            ->respond(new TenantResource($tenant));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->authorizeFromRepo(TenantRepository::class, 'delete', $id);
        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', ['resource' => 'Tenant']))
            ->respond(null);
    }
}
