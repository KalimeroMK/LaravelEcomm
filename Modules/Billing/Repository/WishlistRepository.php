<?php

namespace Modules\Billing\Repository;

use Modules\Billing\Models\Wishlist;
use Modules\Core\Repositories\Repository;

class WishlistRepository extends Repository
{
    /**
     * @var string
     */
    public $model = Wishlist::class;

    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::whereUserId(Auth()->id())->get();
    }

    /**
     * Creates a new Wishlist entry.
     *
     * @param  array<string, mixed>  $data  Data needed for creating the wishlist entry. Expected keys:
     *                                   - 'price' (float): The original price of the item.
     *                                   - 'discount' (float): The discount rate on the item.
     *                                   - 'quantity' (int): The number of items.
     * @return mixed Returns the newly created wishlist entry.
     */
    public function create(array $data): mixed
    {
        $data['price'] = ($data['price'] - ($data['price'] * $data['discount']) / 100);
        $data['amount'] = $data['price'] * $data['quantity'];

        return $this->model::create($data)->fresh();
    }


}
