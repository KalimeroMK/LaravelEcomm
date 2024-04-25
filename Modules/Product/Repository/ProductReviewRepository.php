<?php

namespace Modules\Product\Repository;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Repositories\Repository;
use Modules\Product\Models\ProductReview;

class ProductReviewRepository extends Repository
{
    public Model $model = ProductReview::class;

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