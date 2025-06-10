<?php

declare(strict_types=1);

namespace Modules\Product\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Product\Actions\DeleteProductReviewAction;
use Modules\Product\Actions\GetAllProductReviewsAction;
use Modules\Product\Actions\GetProductReviewsByUserAction;
use Modules\Product\Actions\StoreProductReviewAction;
use Modules\Product\Actions\UpdateProductReviewAction;
use Modules\Product\DTOs\ProductReviewDTO;
use Modules\Product\Http\Requests\ProductReviewStore;
use Modules\Product\Http\Requests\ProductReviewUpdate;
use Modules\Product\Models\ProductReview;

class ProductReviewController extends CoreController
{
    private readonly GetAllProductReviewsAction $getAllProductReviewsAction;

    private readonly GetProductReviewsByUserAction $getProductReviewsByUserAction;

    private readonly StoreProductReviewAction $storeProductReviewAction;

    private readonly UpdateProductReviewAction $updateProductReviewAction;

    private readonly DeleteProductReviewAction $deleteProductReviewAction;

    public function __construct(
        GetAllProductReviewsAction $getAllProductReviewsAction,
        GetProductReviewsByUserAction $getProductReviewsByUserAction,
        StoreProductReviewAction $storeProductReviewAction,
        UpdateProductReviewAction $updateProductReviewAction,
        DeleteProductReviewAction $deleteProductReviewAction
    ) {
        $this->getAllProductReviewsAction = $getAllProductReviewsAction;
        $this->getProductReviewsByUserAction = $getProductReviewsByUserAction;
        $this->storeProductReviewAction = $storeProductReviewAction;
        $this->updateProductReviewAction = $updateProductReviewAction;
        $this->deleteProductReviewAction = $deleteProductReviewAction;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $reviews = auth()->user()->hasRole('client')
            ? $this->getProductReviewsByUserAction->execute()
            : $this->getAllProductReviewsAction->execute();

        return view('product::review.index', ['reviews' => $reviews]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductReviewStore $request): RedirectResponse
    {
        $dto = ProductReviewDTO::fromRequest($request);
        $this->storeProductReviewAction->execute($dto);

        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Application|Factory|View
     */
    public function edit(ProductReview $review)
    {
        $this->authorize('update', $review);

        return view('product::review.edit')->with('review', ProductReviewDTO::fromModel($review));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductReviewUpdate $request, ProductReview $review): RedirectResponse
    {
        $this->authorize('update', $review);
        $dto = ProductReviewDTO::fromRequest($request, $review->id);
        $this->updateProductReviewAction->execute($review->id, $dto);

        return redirect()->route('product::review.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductReview $review): RedirectResponse
    {
        $this->authorize('delete', $review);
        $this->deleteProductReviewAction->execute($review->id);

        return redirect()->route('review.index');
    }
}
