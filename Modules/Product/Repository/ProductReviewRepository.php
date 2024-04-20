<?php

namespace Modules\Product\Repository;

use Modules\Core\Repositories\Repository;
use Modules\Product\Models\ProductReview;

class ProductReviewRepository extends Repository
{
    public \Illuminate\Database\Eloquent\Model $model = ProductReview::class;

    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::getAllReview();
    }

    public function findAllByUser()
    {
        return $this->model::getAllUserReview();
    }
}