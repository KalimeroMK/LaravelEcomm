<?php

namespace Modules\Brand\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Brand\Exceptions\SearchException;
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
        $this->authorizeResource(Brand::class);
    }
    
    /**
     * @param  Search  $request
     *
     * @return ResourceCollection
     * @throws SearchException
     */
    public function index(Search $request): ResourceCollection
    {
        return BrandResource::collection($this->brand_service->getAll($request->validated()));
    }
    
    /**
     * @param  Store  $request
     *
     * @return JsonResponse|string
     */
    public function store(Store $request)
    {
        try {
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
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse|string
     */
    public function show($id)
    {
        try {
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
                ->respond(new BrandResource($this->brand_service->show($id)));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param  Update  $request
     * @param $id
     *
     * @return JsonResponse|string
     */
    public function update(Update $request, $id)
    {
        try {
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
                ->respond(new BrandResource($this->brand_service->update($id, $request->all())));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse|string
     */
    public function destroy($id)
    {
        try {
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
                ->respond($this->brand_service->destroy($id));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}
