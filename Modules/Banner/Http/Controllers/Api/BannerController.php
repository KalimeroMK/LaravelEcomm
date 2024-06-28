<?php

namespace Modules\Banner\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Banner\Http\Requests\Api\Search;
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
    }

    /**
     * @param Search $request
     *
     * @return ResourceCollection
     */
    public function index(Search $request): ResourceCollection
    {
        return BannerResource::collection($this->banner_service->getAll($request->validated()));
    }

    /**
     * @param Store $request
     *
     * @return JsonResponse
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
            ->respond(new BannerResource($this->banner_service->create($request->validated())));
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
                            $this->banner_service->banner_repository->model
                        ),
                    ]
                )
            )
            ->respond(new BannerResource($this->banner_service->findById($id)));
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
                            $this->banner_service->banner_repository->model
                        ),
                    ]
                )
            )
            ->respond(new BannerResource($this->banner_service->update($id, $request->validated())));
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
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
