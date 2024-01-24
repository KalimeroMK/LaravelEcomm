<?php

namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Post\Http\Requests\Api\Search;
use Modules\Post\Http\Requests\Api\Store;
use Modules\Post\Http\Requests\Api\Update;
use Modules\Product\Http\Resources\ProductResource;
use Modules\Product\Models\Product;
use Modules\Product\Service\ProductService;
use ReflectionException;

class ProductController extends CoreController
{

    private ProductService $product_service;

    public function __construct(ProductService $product_service)
    {
        $this->product_service = $product_service;
        $this->authorizeResource(Product::class, 'product');
    }

    public function index(Search $request): ResourceCollection
    {
        return ProductResource::collection($this->product_service->getAll($request->validated()));
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
                            $this->product_service->product_repository->model
                        ),
                    ]
                )
            )
            ->respond(new ProductResource($this->product_service->store($request->validated())));
    }

    /**
     * @throws ReflectionException
     */
    public function show(Product $product): JsonResponse
    {
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
            ->respond(new ProductResource($this->product_service->show($product->id)));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, Product $product): JsonResponse
    {
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
            ->respond(new ProductResource($this->product_service->update($product->id, $request->validated())));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->product_service->destroy($product->id);
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
            ->respond(null);
    }
}
