<?php

declare(strict_types=1);

namespace Modules\Bundle\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Banner\Http\Resource\BannerResource;
use Modules\Bundle\Http\Requests\Store;
use Modules\Bundle\Http\Requests\Update;
use Modules\Bundle\Http\Resource\BundleResource;
use Modules\Bundle\Service\BundleService;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use ReflectionException;

class BundleController extends CoreController
{
    private BundleService $bundleService;

    public function __construct(BundleService $bundleService)
    {
        $this->bundleService = $bundleService;
        $this->middleware('permission:bundle-list', ['only' => ['index']]);
        $this->middleware('permission:bundle-show', ['only' => ['show']]);
        $this->middleware('permission:bundle-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:bundle-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:bundle-delete', ['only' => ['destroy']]);
    }

    public function index(): ResourceCollection
    {
        return BundleResource::collection($this->bundleService->getAll());
    }

    /**
     * @throws Exception
     */
    public function store(Store $request): JsonResponse
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.storeSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->bundleService->bundleRepository->model
                        ),
                    ]
                )
            )
            ->respond(new BannerResource($this->bundleService->create($request->validated())));
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
                            $this->bundleService->bundleRepository->model
                        ),
                    ]
                )
            )
            ->respond(new BannerResource($this->bundleService->findById($id)));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.updateSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->bundleService->bundleRepository->model
                        ),
                    ]
                )
            )
            ->respond(new BannerResource($this->bundleService->update($id, $request->validated())));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->bundleService->delete($id);

        return $this
            ->setMessage(
                __(
                    'apiResponse.deleteSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->bundleService->bundleRepository->model
                        ),
                    ]
                )
            )
            ->respond(null);
    }
}
