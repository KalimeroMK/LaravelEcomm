<?php

namespace Modules\Brand\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Brand\Http\Requests\Api\Search;
use Modules\Brand\Http\Requests\Api\Store;
use Modules\Brand\Http\Requests\Api\Update;
use Modules\Brand\Http\Resource\BrandResource;
use Modules\Brand\Models\Brand;
use Modules\Brand\Service\BrandService;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;

class BrandController extends CoreController
{

    private BrandService $brand_service;

    public function __construct(BrandService $brand_service)
    {
        $this->brand_service = $brand_service;
        $this->authorizeResource(Brand::class, 'brand');
    }

    public function index(Search $request): ResourceCollection
    {
        return BrandResource::collection($this->brand_service->getAll($request->validated()));
    }

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
            ->respond(new BrandResource($this->brand_service->store($request->validated())));
    }

    public function show(Brand $brand): JsonResponse
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
            ->respond(new BrandResource($this->brand_service->show($brand->id)));
    }

    public function update(Update $request, Brand $brand): JsonResponse
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
            ->respond(new BrandResource($this->brand_service->update($brand->id, $request->validated())));
    }

    public function destroy(Brand $brand): JsonResponse
    {
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
            ->respond($this->brand_service->destroy($brand->id));
    }
}
