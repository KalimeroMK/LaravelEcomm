<?php

namespace Modules\Product\Service;

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
     * @return mixed
     */
    public function index(): mixed
    {
        return $this->product_review_repository->findAll();
    }

    /**
     * @param $id
     * @param $data
     *
     * @return mixed
     */
    public function update($id, $data): mixed
    {
        return $this->product_review_repository->update($id, $data);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function edit($id): mixed
    {
        return $this->product_review_repository->findById($id);
    }

    /**
     * @param $request
     *
     * @return void
     */
    public function store($request): void
    {
        $product_info = Product::getProductBySlug($request['slug']);
        $data = $request->all();
        $data['product_id'] = $product_info->id;
        $data['user_id'] = $request->user()->id;
        $data['status'] = 'active';
        ProductReview::create($data);
        $details = [
            'title' => 'New Product Rating!',
            'actionURL' => route('product-detail', $product_info->slug),
            'fas' => 'fa-star',
        ];
        Notification::send(User::role('super-admin')->get(), new StatusNotification($details));
    }

    /**
     * @param $id
     *
     * @return void
     */
    public function destroy($id): void
    {
        $this->product_review_repository->delete($id);
    }

    /**
     * @return mixed
     */
    public function findAllByUser(): mixed
    {
        return $this->product_review_repository->findAllByUser();
    }
}
