<?php

declare(strict_types=1);

namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Product\Actions\DeleteProductAction;
use Modules\Product\Actions\GetAllProductsAction;
use Modules\Product\Actions\StoreProductAction;
use Modules\Product\Actions\UpdateProductAction;
use Modules\Product\DTOs\ProductDTO;
use Modules\Product\Http\Requests\Api\Search;
use Modules\Product\Http\Requests\Api\Store;
use Modules\Product\Http\Requests\Api\Update;
use Modules\Product\Http\Resources\ProductResource;
use Modules\Product\Models\Product;

class ProductController extends CoreController
{
    public function __construct()
    {
        $this->middleware('permission:product-list', ['only' => ['index']]);
        $this->middleware('permission:product-show', ['only' => ['show']]);
        $this->middleware('permission:product-create', ['only' => ['store']]);
        $this->middleware('permission:product-edit', ['only' => ['update']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }

    public function index(Search $request): ResourceCollection
    {
        $productsDto = (new GetAllProductsAction())->execute();

        return ProductResource::collection($productsDto->products);
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        $productDto = (new StoreProductAction())->execute($request->validated());

        return $this->setMessage(__('apiResponse.storeSuccess', ['resource' => 'Product']))->respond(new ProductResource($productDto));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $productDto = new ProductDTO($product);

        return $this->setMessage(__('apiResponse.ok', ['resource' => 'Product']))->respond(new ProductResource($productDto));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $productDto = (new UpdateProductAction())->execute($id, $request->validated());

        return $this->setMessage(__('apiResponse.updateSuccess', ['resource' => 'Product']))->respond(new ProductResource($productDto));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        (new DeleteProductAction())->execute($id);

        return $this->setMessage(__('apiResponse.deleteSuccess', ['resource' => 'Product']))->respond(null);
    }
}
