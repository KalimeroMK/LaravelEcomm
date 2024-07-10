<?php

namespace Modules\Billing\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Billing\Http\Requests\Api\Store;
use Modules\Billing\Http\Resources\WishlistResource;
use Modules\Billing\Service\WishlistService;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use ReflectionException;

class WishlistController extends CoreController
{
    private WishlistService $wishlist_service;

    public function __construct(WishlistService $wishlist_service)
    {
        $this->wishlist_service = $wishlist_service;
    }

    public function index(): ResourceCollection
    {
        return WishlistResource::collection($this->wishlist_service->getAllByUser());
    }

    public function store(Store $request): JsonResponse|string
    {
        try {
            return $this
                ->setMessage(
                    __(
                        'apiResponse.storeSuccess',
                        [
                            'resource' => Helper::getResourceName(
                                $this->wishlist_service->wishlist_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new WishlistResource($this->wishlist_service->create($request->all())));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->wishlist_service->delete($id);

        return $this
            ->setMessage(
                __(
                    'apiResponse.deleteSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->wishlist_service->wishlist_repository->model
                        ),
                    ]
                )
            )
            ->respond(null);
    }
}
