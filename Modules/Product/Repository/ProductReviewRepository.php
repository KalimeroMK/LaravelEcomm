<?php

declare(strict_types=1);

namespace Modules\Product\Repository;

use Illuminate\Support\Collection;
use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Repositories\EloquentRepository;
use Modules\Product\Models\ProductReview;

class ProductReviewRepository extends EloquentRepository implements EloquentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(ProductReview::class);
    }

    /**
     * Find all product reviews.
     *
     * @return Collection<int, ProductReview>
     */
    public function findAll(): Collection
    {
        /** @var class-string<ProductReview> $model */
        $model = $this->modelClass;

        return $model::getAllReview();
    }

    /**
     * Find all product reviews by user.
     *
     * @return Collection<int, ProductReview>
     */
    public function findAllByUser(): Collection
    {
        /** @var class-string<ProductReview> $model */
        $model = $this->modelClass;

        return $model::getAllUserReview();
    }
}
