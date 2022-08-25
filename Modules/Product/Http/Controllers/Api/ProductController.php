<?php

namespace Modules\Product\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Post\Http\Requests\Api\Store;
use Modules\Post\Http\Requests\Api\Update;
use Modules\Post\Http\Resources\PostResource;
use Modules\Product\Http\Resources\ProductResource;
use Modules\Product\Service\ProductService;

class ProductController extends CoreController
{
    
    private ProductService $product_service;
    
    public function __construct(ProductService $product_service)
    {
        $this->product_service = $product_service;
    }
    
    /**
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return ProductResource::collection($this->product_service->findAll());
    }
    
    /**
     *
     * @return mixed
     * @throws Exception
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
                                $this->product_service->product_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new PostResource($this->product_service->store($request->validated())));
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
                                $this->product_service->product_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new ProductResource($this->product_service->show($id)));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    public function update(Update $request, $id)
    {
        try {
            return $this
                ->setMessage(
                    __(
                        'apiResponse.updateSuccess',
                        [
                            'resource' => Helper::getResourceName(
                                $this->product_service->product_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new ProductResource($this->product_service->update($id, $request->validated())));
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
                                $this->product_service->product_repository->model
                            ),
                        ]
                    )
                )
                ->respond($this->product_service->destroy($id));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}
