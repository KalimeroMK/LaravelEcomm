<?php

namespace Modules\Order\Repository;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\Repository;
use Modules\Order\Models\Order;

class OrderRepository extends Repository implements SearchInterface
{
    public $model = Order::class;
    
    /**
     * @return mixed
     */
    public function findAllByUser(): mixed
    {
        return $this->model::with('shipping', 'user', 'carts')->where('user_id', Auth::id())->paginate(10);
    }
    
    /**
     * @param  array  $data
     *
     * @return mixed
     */
    public function search(array $data): mixed
    {
        $query = $this->model::query();
        if (Arr::has($data, 'first_name')) {
            $query->where('first_name', 'like', '%' . Arr::get($data, 'first_name') . '%');
        }
        if (Arr::has($data, 'last_name')) {
            $query->where('last_name', 'like', '%' . Arr::get($data, 'last_name') . '%');
        }
        if (Arr::has($data, 'address1')) {
            $query->where('address1', 'like', '%' . Arr::get($data, 'address1') . '%');
        }
        if (Arr::has($data, 'address2')) {
            $query->where('address2', 'like', '%' . Arr::get($data, 'address2') . '%');
        }
        if (Arr::has($data, 'coupon')) {
            $query->where('coupon', 'like', '%' . Arr::get($data, 'coupon') . '%');
        }
        if (Arr::has($data, 'phone')) {
            $query->where('phone', 'like', '%' . Arr::get($data, 'phone') . '%');
        }
        if (Arr::has($data, 'post_code')) {
            $query->where('post_code', 'like', '%' . Arr::get($data, 'post_code') . '%');
        }
        if (Arr::has($data, 'email')) {
            $query->where('email', 'like', '%' . Arr::get($data, 'email') . '%');
        }
        if (Arr::has($data, 'all_included') && (bool)Arr::get($data, 'all_included') === true || empty($data)) {
            return $query->with('shipping', 'user', 'carts')->get();
        }
        $query->orderBy(Arr::get($data, 'order_by') ?? 'id', Arr::get($data, 'sort') ?? 'desc');
        
        return $query->with('shipping', 'user', 'carts')->paginate(
            Arr::get($data, 'per_page') ?? (new $this->model)->getPerPage()
        );
    }
}