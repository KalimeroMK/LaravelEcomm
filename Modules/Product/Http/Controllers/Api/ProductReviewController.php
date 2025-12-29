<?php

declare(strict_types=1);

namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Product\Actions\DeleteProductReviewAction;
use Modules\Product\Actions\FindProductReviewAction;
use Modules\Product\Actions\GetAllProductReviewsAction;
use Modules\Product\Actions\GetProductReviewsByUserAction;
use Modules\Product\Actions\StoreProductReviewAction;
use Modules\Product\Actions\UpdateProductReviewAction;
use Modules\Product\DTOs\ProductReviewDTO;
use Modules\Product\Http\Requests\Api\ProductReviewStore;
use Modules\Product\Http\Requests\Api\ProductReviewUpdate;
use Modules\Product\Http\Resources\ProductReviewResource;
use Modules\Product\Models\ProductReview;
use Modules\Product\Repository\ProductReviewRepository;

class ProductReviewController extends CoreController
{
    public function __construct(
        private readonly GetAllProductReviewsAction $getAllProductReviewsAction,
        private readonly GetProductReviewsByUserAction $getProductReviewsByUserAction,
        private readonly StoreProductReviewAction $storeProductReviewAction,
        private readonly UpdateProductReviewAction $updateProductReviewAction,
        private readonly DeleteProductReviewAction $deleteProductReviewAction,
        private readonly FindProductReviewAction $findProductReviewAction,
        private readonly ProductReviewRepository $repository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', ProductReview::class);

        $reviews = auth()->user()->hasRole('client')
            ? $this->getProductReviewsByUserAction->execute()
            : $this->getAllProductReviewsAction->execute();

        return ProductReviewResource::collection($reviews);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductReviewStore $request): JsonResponse
    {
        $this->authorize('create', ProductReview::class);

        $dto = ProductReviewDTO::fromRequest($request);
        $review = $this->storeProductReviewAction->execute($dto);

        return $this
            ->setMessage(__('apiResponse.storeSuccess', ['resource' => 'Product Review']))
            ->respond(new ProductReviewResource($review->load('user', 'product')));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $review = $this->findProductReviewAction->execute($id);
        $this->authorize('view', $review);

        return $this
            ->setMessage(__('apiResponse.ok', ['resource' => 'Product Review']))
            ->respond(new ProductReviewResource($review));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductReviewUpdate $request, int $id): JsonResponse
    {
        $review = $this->authorizeFromRepo(ProductReviewRepository::class, 'update', $id);

        $dto = ProductReviewDTO::fromRequest($request, $id);
        $review = $this->updateProductReviewAction->execute($id, $dto);

        return $this
            ->setMessage(__('apiResponse.updateSuccess', ['resource' => 'Product Review']))
            ->respond(new ProductReviewResource($review->load('user', 'product')));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->authorizeFromRepo(ProductReviewRepository::class, 'delete', $id);

        $this->deleteProductReviewAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', ['resource' => 'Product Review']))
            ->respond(null);
    }
}
