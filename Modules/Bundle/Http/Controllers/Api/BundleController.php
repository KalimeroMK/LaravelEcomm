<?php

namespace Modules\Bundle\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Banner\Http\Resource\BannerResource;
use Modules\Banner\Models\Banner;
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
        $this->authorizeResource(Banner::class);
    }

    /**
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return BundleResource::collection($this->bundleService->getAll());
    }

    /**
     * @param Store $request
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function store(Store $request)
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
            ->respond(new BannerResource($this->bundleService->store($request->validated())));
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function show(int $id)
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
     * @param Update $request
     * @param int $id
     *
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function update(Update $request, int $id)
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
     * @param int $id
     *
     * @return JsonResponse
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
