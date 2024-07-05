<?php

namespace Modules\Billing\Repository;

use Illuminate\Database\Eloquent\Model;
use Modules\Billing\Models\Wishlist;
use Modules\Core\Repositories\Repository;

class WishlistRepository extends Repository
{
    /**
     * @var string
     */
    public $model = Wishlist::class;

    /**
     * Creates a new Wishlist entry.
     *
     * @param  array<string, mixed>  $data  Data needed for creating the wishlist entry. Expected keys:
     *                                      - 'price' (float): The original price of the item.
     *                                      - 'discount' (float): The discount rate on the item.
     *                                      - 'quantity' (int): The number of items.
     * @return Model Returns the newly created wishlist entry.
     */
    public function create(array $data): Model
    {
        $data['price'] = ($data['price'] - ($data['price'] * $data['discount']) / 100);
        $data['amount'] = $data['price'] * $data['quantity'];

        return $this->model::create($data)->fresh();
    }
}
