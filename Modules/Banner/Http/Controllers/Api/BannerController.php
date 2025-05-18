<?php

declare(strict_types=1);

namespace Modules\Banner\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Banner\Http\Requests\Api\Store;
use Modules\Banner\Http\Requests\Api\Update;
use Modules\Banner\Http\Resource\BannerResource;
use Modules\Banner\Service\BannerService;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use ReflectionException;

class BannerController extends CoreController
{
    private BannerService $banner_service;

    public function __construct(BannerService $banner_service)
    {
        $this->banner_service = $banner_service;
        $this->middleware('permission:banner-list', ['only' => ['index']]);
        $this->middleware('permission:banner-show', ['only' => ['show']]);
        $this->middleware('permission:banner-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:banner-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:banner-delete', ['only' => ['destroy']]);
    }

    public function index(): ResourceCollection
    {
        return BannerResource::collection($this->banner_service->getAll());
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
                            $this->banner_service->banner_repository->model
                        ),
                    ]
                )
            )
            ->respond(new BannerResource($this->banner_service->createWithMedia($request->validated())));
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
                            $this->banner_service->banner_repository->model
                        ),
                    ]
                )
            )
            ->respond(new BannerResource($this->banner_service->findById($id)));
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
                            $this->banner_service->banner_repository->model
                        ),
                    ]
                )
            )
            ->respond(new BannerResource($this->banner_service->updateWithMedia($id, $request->validated())));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->banner_service->delete($id);

        return $this
            ->setMessage(
                __(
                    'apiResponse.deleteSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->banner_service->banner_repository->model
                        ),
                    ]
                )
            )
            ->respond(null);
    }
}
