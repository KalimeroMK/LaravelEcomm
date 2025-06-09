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
use Modules\Coupon\Repository\CouponRepository;
use ReflectionException;

class CouponController extends CoreController
{
    public function __construct(
        private readonly CreateCouponAction $createAction,
        private readonly UpdateCouponAction $updateAction,
        private readonly DeleteCouponAction $deleteAction
    ) {
        // permission middleware removed — now using policies
    }

    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Coupon::class);

        return CouponResource::collection(Coupon::all());
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', Coupon::class);

        $coupon = $this->createAction->execute(CouponDTO::fromRequest($request));

        return $this
            ->setMessage(__('apiResponse.storeSuccess', [
                'resource' => Helper::getResourceName(Coupon::class),
            ]))
            ->respond(new CouponResource($coupon));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $coupon = $this->authorizeFromRepo(CouponRepository::class, 'view', $id);

        return $this
            ->setMessage(__('apiResponse.ok', [
                'resource' => Helper::getResourceName(Coupon::class),
            ]))
            ->respond(new CouponResource($coupon));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $this->authorizeFromRepo(CouponRepository::class, 'update', $id);

        $dto = CouponDTO::fromRequest($request, $id);
        $coupon = $this->updateAction->execute($dto);

        return $this
            ->setMessage(__('apiResponse.updateSuccess', [
                'resource' => Helper::getResourceName(Coupon::class),
            ]))
            ->respond(new CouponResource($coupon));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->authorizeFromRepo(CouponRepository::class, 'delete', $id);

        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', [
                'resource' => Helper::getResourceName(Coupon::class),
            ]))
            ->respond(null);
    }
}
