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
use Modules\Cart\Http\Resources\CartResource;
use Modules\Cart\Models\Cart;
use Modules\Cart\Repository\CartRepository;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use ReflectionException;

class CartController extends CoreController
{
    public readonly CartRepository $repository;

    private readonly CreateCartAction $createAction;

    private readonly UpdateCartAction $updateAction;

    private readonly DeleteCartAction $deleteAction;

    public function __construct(
        CartRepository $repository,
        CreateCartAction $createAction,
        UpdateCartAction $updateAction,
        DeleteCartAction $deleteAction
    ) {
        $this->repository = $repository;
        $this->createAction = $createAction;
        $this->updateAction = $updateAction;
        $this->deleteAction = $deleteAction;
        // Removed permission middleware
    }

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

        return $this
            ->setMessage(__('apiResponse.storeSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new CartResource(
                $this->createAction->execute(CartDTO::fromArray($request->all()))
            ));
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

    public function update(Store $request, int $id): JsonResponse
    {
        $this->authorizeFromRepo(CartRepository::class, 'update', $id);

        return $this
            ->setMessage(__('apiResponse.updateSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new CartResource(
                $this->updateAction->execute(CartDTO::fromArray($request->all())->withId($id))
            ));
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
