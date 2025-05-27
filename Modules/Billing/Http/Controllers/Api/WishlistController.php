<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Billing\Actions\Wishlist\CreateWishlistAction;
use Modules\Billing\Actions\Wishlist\DeleteWishlistAction;
use Modules\Billing\DTOs\WishlistDTO;
use Modules\Billing\Http\Requests\Api\Store;
use Modules\Billing\Http\Resources\WishlistResource;
use Modules\Billing\Repository\WishlistRepository;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use ReflectionException;

class WishlistController extends CoreController
{
    private WishlistRepository $repository;

    private CreateWishlistAction $createAction;

    private DeleteWishlistAction $deleteAction;

    public function __construct(
        WishlistRepository $repository,
        CreateWishlistAction $createAction,
        DeleteWishlistAction $deleteAction
    ) {
        $this->repository = $repository;
        $this->createAction = $createAction;
        $this->deleteAction = $deleteAction;
    }

    public function index(): ResourceCollection
    {
        return WishlistResource::collection($this->repository->findBy('user_id', auth()->user()->id));
    }

    public function store(Store $request): JsonResponse|string
    {
        try {
            $dto = WishlistDTO::fromRequest($request);
            $wishlist = $this->createAction->execute($dto);

            return $this
                ->setMessage(
                    __(
                        'apiResponse.storeSuccess',
                        [
                            'resource' => Helper::getResourceName(
                                $this->repository->modelClass
                            ),
                        ]
                    )
                )
                ->respond(new WishlistResource($wishlist));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->deleteAction->execute($id);

        return $this
            ->setMessage(
                __(
                    'apiResponse.deleteSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->repository->modelClass
                        ),
                    ]
                )
            )
            ->respond(null);
    }
}
