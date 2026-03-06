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
use Modules\Coupon\Actions\ValidateCouponAction;
use Modules\Coupon\Models\CouponUsage;
use Illuminate\Support\Facades\Auth;
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
        private readonly DeleteCouponAction $deleteAction,
        private readonly ValidateCouponAction $validateCouponAction,
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

    /**
     * Validate a coupon code without applying it
     */
    public function validateCoupon(\Illuminate\Http\Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string']);
        
        $user = Auth::user();
        $isValid = $this->validateCouponAction->isValid(
            $request->code,
            $user?->id,
            session()->getId(),
            $user?->customer_group_id ?? null
        );

        if ($isValid) {
            $coupon = Coupon::byCode($request->code)->first();
            return $this
                ->setMessage('Coupon is valid.')
                ->respond([
                    'valid' => true,
                    'coupon' => new CouponResource($coupon),
                ]);
        }

        return $this
            ->setMessage('Coupon is invalid or cannot be applied.')
            ->setStatusCode(422)
            ->respond(['valid' => false]);
    }

    /**
     * Get coupon usage statistics
     */
    public function usage(int $id): JsonResponse
    {
        $this->authorizeFromRepo(CouponRepository::class, 'view', $id);

        $coupon = Coupon::findOrFail($id);
        $usages = CouponUsage::forCoupon($id)
            ->with(['user', 'order'])
            ->orderBy('used_at', 'desc')
            ->paginate(20);

        return $this
            ->setMessage('Coupon usage statistics retrieved.')
            ->respond([
                'coupon' => new CouponResource($coupon),
                'usage_stats' => [
                    'total_usage' => $coupon->times_used,
                    'usage_limit' => $coupon->usage_limit,
                    'remaining' => $coupon->usage_limit ? $coupon->usage_limit - $coupon->times_used : null,
                ],
                'usages' => $usages,
            ]);
    }
}
