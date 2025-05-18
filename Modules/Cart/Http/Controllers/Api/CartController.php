<?php

declare(strict_types=1);

namespace Modules\Cart\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Cart\Http\Requests\Api\Store;
use Modules\Cart\Http\Resources\CartResource;
use Modules\Cart\Service\CartService;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use ReflectionException;

class CartController extends CoreController
{
    private CartService $cart_service;

    public function __construct(CartService $cart_service)
    {
        $this->cart_service = $cart_service;
        $this->middleware('permission:cart-list', ['only' => ['index']]);
        $this->middleware('permission:cart-show', ['only' => ['show']]);
        $this->middleware('permission:cart-create', ['only' => ['store']]);
        $this->middleware('permission:cart-edit', ['only' => ['update']]);
        $this->middleware('permission:cart-delete', ['only' => ['destroy']]);
    }

    public function index(): ResourceCollection
    {
        return CartResource::collection($this->cart_service->getAll());
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse|string
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.storeSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->cart_service->cart_repository->model
                        ),
                    ]
                )
            )
            ->respond(new CartResource($this->cart_service->apiAddToCart($request->all())));
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
                            $this->cart_service->cart_repository->model
                        ),
                    ]
                )
            )
            ->respond(new CartResource($this->cart_service->show($id)));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Store $request): JsonResponse
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.updateSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->cart_service->cart_repository->model
                        ),
                    ]
                )
            )
            ->respond(new CartResource($this->cart_service->apiUpdateCart($request->all())));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->cart_service->destroy($id);

        return $this
            ->setMessage(
                __(
                    'apiResponse.deleteSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->cart_service->cart_repository->model
                        ),
                    ]
                )
            )
            ->respond(null);
    }
}
