<?php

declare(strict_types=1);

namespace Modules\Coupon\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Coupon\Actions\Coupon\CreateCouponAction;
use Modules\Coupon\Actions\Coupon\DeleteCouponAction;
use Modules\Coupon\Actions\Coupon\UpdateCouponAction;
use Modules\Coupon\DTOs\CouponDTO;
use Modules\Coupon\Http\Requests\Api\Store;
use Modules\Coupon\Http\Requests\Api\Update;
use Modules\Coupon\Http\Resource\CouponResource;
use Modules\Coupon\Models\Coupon;

class CouponController extends CoreController
{
    private CreateCouponAction $createAction;

    private UpdateCouponAction $updateAction;

    private DeleteCouponAction $deleteAction;

    public function __construct(
        CreateCouponAction $createAction,
        UpdateCouponAction $updateAction,
        DeleteCouponAction $deleteAction
    ) {
        $this->createAction = $createAction;
        $this->updateAction = $updateAction;
        $this->deleteAction = $deleteAction;
        $this->middleware('permission:coupon-list', ['only' => ['index']]);
        $this->middleware('permission:coupon-show', ['only' => ['show']]);
        $this->middleware('permission:coupon-create', ['only' => ['store']]);
        $this->middleware('permission:coupon-edit', ['only' => ['update']]);
        $this->middleware('permission:coupon-delete', ['only' => ['destroy']]);
    }

    public function index(): ResourceCollection
    {
        return CouponResource::collection(Coupon::all());
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        $dto = CouponDTO::fromRequest($request);
        $coupon = $this->createAction->execute($dto);

        return $this
            ->setMessage(
                __(
                    'apiResponse.storeSuccess',
                    [
                        'resource' => Helper::getResourceName(Coupon::class),
                    ]
                )
            )
            ->respond(new CouponResource($coupon));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $coupon = Coupon::findOrFail($id);

        return $this
            ->setMessage(
                __(
                    'apiResponse.ok',
                    [
                        'resource' => Helper::getResourceName(Coupon::class),
                    ]
                )
            )
            ->respond(new CouponResource($coupon));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $dto = CouponDTO::fromRequest($request, $id);
        $coupon = $this->updateAction->execute($dto);

        return $this
            ->setMessage(
                __(
                    'apiResponse.updateSuccess',
                    [
                        'resource' => Helper::getResourceName(Coupon::class),
                    ]
                )
            )
            ->respond(new CouponResource($coupon));
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
                        'resource' => Helper::getResourceName(Coupon::class),
                    ]
                )
            )
            ->respond(null);
    }
}
