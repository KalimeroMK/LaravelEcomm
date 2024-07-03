<?php

namespace Modules\Product\Service;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Modules\Core\Notifications\StatusNotification;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductReview;
use Modules\Product\Repository\ProductReviewRepository;
use Modules\User\Models\User;

class ProductReviewService
{
    private ProductReviewRepository $product_review_repository;

    public function __construct(ProductReviewRepository $product_review_repository)
    {
        $this->product_review_repository = $product_review_repository;
    }

    /**
     * Get all product reviews.
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return $this->product_review_repository->findAll();
    }

    /**
     * Update a product review.
     *
     * @param  int  $id
     * @param  array<string, mixed>  $data
     * @return mixed
     */
    public function update(int $id, array $data): mixed
    {
        return $this->product_review_repository->update($id, $data);
    }

    /**
     * Edit a product review.
     *
     * @param  int  $id
     * @return mixed
     */
    public function edit(int $id): mixed
    {
        return $this->product_review_repository->findById($id);
    }

    /**
     * Store a newly created product review.
     *
     * @param  Request  $request
     * @return void
     */
    public function store(Request $request): void
    {
        $product_info = Product::getProductBySlug($request['slug']);
        $data = $request->all();
        $data['product_id'] = $product_info->id;
        $data['user_id'] = $request->user()->id;
        $data['status'] = 'active';
        ProductReview::create($data);
        $details = [
            'title' => 'New Product Rating!',
            'actionURL' => route('front.product-detail', $product_info->slug),
            'fas' => 'fa-star',
        ];
        Notification::send(User::role('super-admin')->get(), new StatusNotification($details));
    }


    /**
     * Delete a product review.
     *
     * @param  int  $id
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->product_review_repository->delete($id);
    }

    /**
     * Find all product reviews by user.
     *
     * @return Collection
     */
    public function findAllByUser(): Collection
    {
        return $this->product_review_repository->findAllByUser();
    }
}
