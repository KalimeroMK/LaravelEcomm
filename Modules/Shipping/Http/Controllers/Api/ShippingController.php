<?php

declare(strict_types=1);

namespace Modules\Shipping\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Shipping\Actions\DeleteShippingAction;
use Modules\Shipping\Actions\FindShippingAction;
use Modules\Shipping\Actions\GetAllShippingsAction;
use Modules\Shipping\Actions\StoreShippingAction;
use Modules\Shipping\Actions\UpdateShippingAction;
use Modules\Shipping\Http\Requests\Api\Store;
use Modules\Shipping\Http\Requests\Api\Update;
use Modules\Shipping\Http\Resources\ShippingResource;
use ReflectionException;

class ShippingController extends CoreController
{
    public function __construct()
    {
        $this->middleware('permission:shipping-list', ['only' => ['index']]);
        $this->middleware('permission:shipping-show', ['only' => ['show']]);
        $this->middleware('permission:shipping-create', ['only' => ['store']]);
        $this->middleware('permission:shipping-edit', ['only' => ['update']]);
        $this->middleware('permission:shipping-delete', ['only' => ['destroy']]);
    }

    public function index(): ResourceCollection
    {
        $shippingsDto = (new GetAllShippingsAction())->execute();

        return ShippingResource::collection($shippingsDto->shippings);
    }

    /**
     * @throws Exception
     */
    public function store(Store $request): JsonResponse
    {
        $shipping = (new StoreShippingAction())->execute($request->validated());

        return $this
            ->setMessage(__('apiResponse.storeSuccess', ['resource' => 'Shipping']))
            ->respond(new ShippingResource($shipping));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $shippingDto = (new FindShippingAction())->execute($id);

        return $this
            ->setMessage(__('apiResponse.ok', ['resource' => 'Shipping']))
            ->respond(new ShippingResource((object) $shippingDto->shipping));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        (new UpdateShippingAction())->execute($id, $request->validated());
        $shippingDto = (new FindShippingAction())->execute($id);

        return $this
            ->setMessage(__('apiResponse.updateSuccess', ['resource' => 'Shipping']))
            ->respond(new ShippingResource((object) $shippingDto->shipping));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        (new DeleteShippingAction())->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', ['resource' => 'Shipping']))
            ->respond(null);
    }
}
