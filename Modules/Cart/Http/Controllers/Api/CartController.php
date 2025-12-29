<?php

declare(strict_types=1);

namespace Modules\Cart\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Cart\Actions\CreateCartAction;
use Modules\Cart\Actions\DeleteCartAction;
use Modules\Cart\Actions\FindCartAction;
use Modules\Cart\Actions\GetUserCartAction;
use Modules\Cart\Actions\UpdateCartAction;
use Modules\Cart\Actions\UpdateCartItemsAction;
use Modules\Cart\DTOs\CartDTO;
use Modules\Cart\Http\Requests\Api\Store;
use Modules\Cart\Http\Requests\Api\Update;
use Modules\Cart\Http\Resources\CartResource;
use Modules\Cart\Models\Cart;
use Modules\Cart\Repository\CartRepository;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Product\Actions\FindProductBySlugAction;
use ReflectionException;

class CartController extends CoreController
{
    public function __construct(
        private readonly CartRepository $repository,
        private readonly GetUserCartAction $getUserCartAction,
        private readonly FindCartAction $findCartAction,
        private readonly FindProductBySlugAction $findProductBySlugAction,
        private readonly CreateCartAction $createAction,
        private readonly UpdateCartAction $updateAction,
        private readonly UpdateCartItemsAction $updateCartItemsAction,
        private readonly DeleteCartAction $deleteAction
    ) {}

    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Cart::class);

        $carts = $this->getUserCartAction->execute();

        return CartResource::collection($carts);
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
        $cart = $this->findCartAction->execute($id);
        $this->authorize('view', $cart);

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
        $cart = $this->findCartAction->execute($id);
        $this->authorize('delete', $cart);

        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', [
                'resource' => 'Cart',
            ]))
            ->respond(null);
    }

    /**
     * Add product to cart by slug.
     */
    public function addToCart(string $slug): JsonResponse
    {
        $this->authorize('create', Cart::class);

        $product = $this->findProductBySlugAction->execute($slug);

        if (! $product) {
            return $this
                ->setMessage('Product not found.')
                ->setStatusCode(404)
                ->respond(null);
        }

        $dto = new CartDTO(
            id: null,
            product_id: $product->id,
            quantity: 1,
            user_id: auth()->id(),
            price: $product->price,
            session_id: session()->getId(),
            amount: $product->price,
            order_id: null,
        );

        $cart = $this->createAction->execute($dto);

        return $this
            ->setMessage('Product added to cart successfully.')
            ->respond(new CartResource($cart));
    }

    /**
     * Update multiple cart items.
     */
    public function updateCartItems(\Illuminate\Http\Request $request): JsonResponse
    {
        $this->authorize('update', Cart::class);

        $this->updateCartItemsAction->execute($request);

        $carts = $this->getUserCartAction->execute();

        return $this
            ->setMessage('Cart updated successfully.')
            ->respond(CartResource::collection($carts));
    }
}
