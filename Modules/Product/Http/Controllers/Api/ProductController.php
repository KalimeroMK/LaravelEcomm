<?php

declare(strict_types=1);

namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Core\Support\Media\MediaUploader;
use Modules\Core\Support\Relations\SyncRelations;
use Modules\Product\Actions\DeleteProductAction;
use Modules\Product\Actions\StoreProductAction;
use Modules\Product\Actions\UpdateProductAction;
use Modules\Product\DTOs\ProductDTO;
use Modules\Product\Http\Requests\Api\Search;
use Modules\Product\Http\Requests\Api\Store;
use Modules\Product\Http\Requests\Api\Update;
use Modules\Product\Http\Resources\ProductResource;
use Modules\Product\Models\Product;
use Modules\Product\Repository\ProductRepository;

class ProductController extends CoreController
{
    public function __construct(
        public readonly ProductRepository $repository,
        private readonly StoreProductAction $storeProductAction,
        private readonly UpdateProductAction $updateProductAction,
        private readonly DeleteProductAction $deleteProductAction
    ) {}

    public function index(Search $request): ResourceCollection
    {
        $this->authorize('viewAny', Product::class);
        $products = $this->repository->findAll();

        return ProductResource::collection($products);
    }

    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', Product::class);

        $dto = ProductDTO::fromRequest($request);
        $product = $this->storeProductAction->execute($dto);

        SyncRelations::execute($product,
            ['categories' => $dto->categories, 'tags' => $dto->tags, 'brand' => $dto->brand_id]);
        MediaUploader::uploadMultiple($product, ['images'], 'product');

        return $this
            ->setMessage(__('apiResponse.storeSuccess', ['resource' => 'Product']))
            ->respond(new ProductResource($product->fresh('media')));
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->authorizeFromRepo(ProductRepository::class, 'view', $id);

        return $this
            ->setMessage(__('apiResponse.ok', ['resource' => 'Product']))
            ->respond(new ProductResource($product));
    }

    public function update(Update $request, int $id): JsonResponse
    {
        $product = $this->authorizeFromRepo(ProductRepository::class, 'update', $id);

        $dto = ProductDTO::fromRequest($request, $id, $product);
        $product = $this->updateProductAction->execute($id, $dto);

        SyncRelations::execute($product,
            ['categories' => $dto->categories, 'tags' => $dto->tags, 'brand' => $dto->brand_id]);
        /** @var Product $product */
        MediaUploader::clearAndUpload($product, ['images'], 'post');

        return $this
            ->setMessage(__('apiResponse.updateSuccess', ['resource' => 'Product']))
            ->respond(new ProductResource($product->fresh('media')));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->authorizeFromRepo(ProductRepository::class, 'delete', $id);

        $this->deleteProductAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', ['resource' => 'Product']))
            ->respond(null);
    }
}
