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
use ReflectionException;

class BrandController extends CoreController
{

    private BrandService $brand_service;

    public function __construct(BrandService $brand_service)
    {
        $this->brand_service = $brand_service;
        $this->authorizeResource(Brand::class, 'brands');
    }

    public function index(Search $request): ResourceCollection
    {
        return BrandResource::collection($this->brand_service->getAll($request->validated()));
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
            ->respond(new BrandResource($this->brand_service->store($request->validated())));
    }

    /**
     * @throws ReflectionException
     */
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

    /**
     * @throws ReflectionException
     */
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

    /**
     * @param  int  $id
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function destroy(int $id)
    {
        $this->brand_service->destroy($id);

        $resourceName = Helper::getResourceName(
            $this->brand_service->brand_repository->model
        );

        $message = __('apiResponse.deleteSuccess', ['resource' => $resourceName]);

        // Assuming you have a method to set response message and status
        return $this->setMessage($message)->respond(null);
    }
}
