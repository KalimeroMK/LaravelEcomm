<?php

namespace Modules\Product\Repository;

use Illuminate\Support\Collection;
use Modules\Core\Repositories\Repository;
use Modules\Product\Models\ProductReview;

class ProductReviewRepository extends Repository
{
    /**
     * The model that the repository works with.
     *
     * @var string
     */
    public $model = ProductReview::class;

    /**
     * Find all product reviews.
     *
     * @return Collection<int, ProductReview>
     */
    public function findAll(): Collection
    {
        return $this->model::getAllReview();
    }

    /**
     * Find all product reviews by user.
     *
     * @return Collection<int, ProductReview>
     */
    public function findAllByUser(): Collection
    {
        return $this->model::getAllUserReview();
    }
}
