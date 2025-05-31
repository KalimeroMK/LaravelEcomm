<?php

declare(strict_types=1);

namespace Modules\Shipping\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Shipping\Actions\DeleteShippingAction;
use Modules\Shipping\Actions\GetAllShippingAction;
use Modules\Shipping\Actions\StoreShippingAction;
use Modules\Shipping\Actions\UpdateShippingAction;
use Modules\Shipping\DTOs\ShippingDTO;
use Modules\Shipping\Http\Requests\Api\Store;
use Modules\Shipping\Http\Requests\Api\Update;
use Modules\Shipping\Http\Resources\ShippingResource;
use Modules\Shipping\Models\Shipping;
use Modules\Shipping\Repository\ShippingRepository;

class ShippingController extends CoreController
{
    private readonly GetAllShippingAction $getAllAction;

    private readonly StoreShippingAction $storeAction;

    private readonly UpdateShippingAction $updateAction;

    private readonly DeleteShippingAction $deleteAction;

    public function __construct(
        GetAllShippingAction $getAllAction,
        StoreShippingAction $storeAction,
        UpdateShippingAction $updateAction,
        DeleteShippingAction $deleteAction,
    ) {
        $this->getAllAction = $getAllAction;
        $this->storeAction = $storeAction;
        $this->updateAction = $updateAction;
        $this->deleteAction = $deleteAction;
    }

    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Shipping::class);

        return ShippingResource::collection($this->getAllAction->execute());
    }

    /**
     * @throws Exception
     */
    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', Shipping::class);
        $shipping = $this->storeAction->execute(ShippingDTO::fromRequest($request));

        return $this
            ->setMessage(__('apiResponse.storeSuccess', ['resource' => 'Shipping']))
            ->respond(new ShippingResource($shipping));
    }

    public function show(int $id): JsonResponse
    {
        $shipping = $this->authorizeFromRepo(ShippingRepository::class, 'view', $id);

        return $this
            ->setMessage(__('apiResponse.ok', ['resource' => 'Shipping']))
            ->respond(new ShippingResource($shipping));
    }

    public function update(Update $request, int $id): JsonResponse
    {
        $existingProduct = $this->authorizeFromRepo(ShippingRepository::class, 'view', $id);

        $shipping = $this->storeAction->execute(ShippingDTO::fromRequest($request, $id, $existingProduct));

        return $this
            ->setMessage(__('apiResponse.updateSuccess', ['resource' => 'Shipping']))
            ->respond(new ShippingResource($shipping));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->authorizeFromRepo(ShippingRepository::class, 'delete', $id);
        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', ['resource' => 'Shipping']))
            ->respond(null);
    }
}
