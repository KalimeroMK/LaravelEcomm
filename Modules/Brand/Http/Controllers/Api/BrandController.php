<?php

namespace Modules\Brand\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Brand\Http\Requests\Api\Search;
use Modules\Brand\Http\Requests\Api\Store;
use Modules\Brand\Http\Requests\Api\Update;
use Modules\Brand\Http\Resource\BrandResource;
use Modules\Brand\Service\BrandService;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use ReflectionException;

class BrandController extends CoreController
{
    private BrandService $brand_service;

    public function __construct(BrandService $brand_service)
    {
        $this->brand_service = $brand_service;
    }

    public function index(Search $request): ResourceCollection
    {
        return BrandResource::collection($this->brand_service->search($request->validated()));
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.storeSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->brand_service->brand_repository->model
                        ),
                    ]
                )
            )
            ->respond(new BrandResource($this->brand_service->create($request->validated())));
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
                            $this->brand_service->brand_repository->model
                        ),
                    ]
                )
            )
            ->respond(new BrandResource($this->brand_service->findById($id)));
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
                            $this->brand_service->brand_repository->model
                        ),
                    ]
                )
            )
            ->respond(new BrandResource($this->brand_service->update($id, $request->validated())));
    }

    /**
     * @throws ReflectionException
     */
    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->brand_service->delete($id);

        return $this
            ->setMessage(
                __(
                    'apiResponse.deleteSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->brand_service->brand_repository->model
                        ),
                    ]
                )
            )
            ->respond(null);
    }
}
