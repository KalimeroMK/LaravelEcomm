<?php

declare(strict_types=1);

namespace Modules\Billing\Repository;

use Illuminate\Database\Eloquent\Model;
use Modules\Billing\Models\Wishlist;
use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Repositories\EloquentRepository;

class WishlistRepository extends EloquentRepository implements EloquentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(Wishlist::class);
    }

    /**
     * Creates a new Wishlist entry with calculated amount and discounted price.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Model
    {
        $data['price'] -= ($data['price'] * $data['discount']) / 100;
        $data['amount'] = $data['price'] * $data['quantity'];

        /** @var class-string<Model> $model */
        $model = $this->modelClass;

        return $model::create($data)->fresh();
    }
}
