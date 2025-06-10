<?php

declare(strict_types=1);

namespace Modules\Cart\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Cart\Actions\CreateCartAction;
use Modules\Cart\Actions\DeleteCartAction;
use Modules\Cart\Actions\UpdateCartAction;
use Modules\Cart\DTOs\CartDTO;
use Modules\Cart\Http\Requests\Api\Store;
use Modules\Cart\Http\Requests\Api\Update;
use Modules\Cart\Http\Resources\CartResource;
use Modules\Cart\Models\Cart;
use Modules\Cart\Repository\CartRepository;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use ReflectionException;

class CartController extends CoreController
{
    public function __construct(
        private readonly CartRepository $repository,
        private readonly CreateCartAction $createAction,
        private readonly UpdateCartAction $updateAction,
        private readonly DeleteCartAction $deleteAction
    ) {}

    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Cart::class);

        return CartResource::collection($this->repository->findAll());
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', Cart::class);
        $cart = $this->createAction->execute(CartDTO::fromRequest($request));

        return $this
            ->setMessage(__('apiResponse.storeSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new CartResource($cart));
    }

    public function show(int $id): JsonResponse
    {
        $cart = $this->authorizeFromRepo(CartRepository::class, 'view', $id);

        return $this
            ->setMessage(__('apiResponse.ok', [
                'resource' => 'Cart',
            ]))
            ->respond(new CartResource($cart));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $this->authorizeFromRepo(CartRepository::class, 'update', $id);
        $cart = $this->updateAction->execute(CartDTO::fromRequest($request, $id, $this->repository->findById($id)));

        return $this
            ->setMessage(__('apiResponse.updateSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new CartResource($cart));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->authorizeFromRepo(CartRepository::class, 'delete', $id);

        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', [
                'resource' => 'Cart',
            ]))
            ->respond(null);
    }
}
