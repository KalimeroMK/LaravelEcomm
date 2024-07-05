<?php

namespace Modules\Product\Http\Controllers\Api;

use Exception;
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
            ->respond(new ProductResource($this->product_service->create($request->validated())));
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
            ->respond(new ProductResource($this->product_service->findById($product->id)));
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function update(Update $request, $id): JsonResponse
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
            ->respond(new ProductResource($this->product_service->update($id, $request->validated())));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy($id): JsonResponse
    {
        $this->product_service->delete($id);

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
