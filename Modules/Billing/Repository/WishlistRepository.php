<?php

namespace Modules\Billing\Repository;

use Modules\Billing\Models\Wishlist;
use Modules\Core\Repositories\Repository;

class WishlistRepository extends Repository
{
    public $model = Wishlist::class;
    
    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::whereUserId(Auth()->id())->get();
    }
    
    /**
     * @param  array  $data
     *
     * @return mixed
     */
    public function create(array $data): mixed
    {
        $data['price']  = ($data['price'] - ($data['price'] * $data['discount']) / 100);
        $data['amount'] = $data['price'] * $data['quantity'];
        
        return $this->model::create($data)->fresh();
    }
    
}
