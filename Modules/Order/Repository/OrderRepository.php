<?php

namespace Modules\Order\Repository;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\Repository;
use Modules\Order\Models\Order;

class OrderRepository extends Repository implements SearchInterface
{
    public $model = Order::class;

    public function findAllByUser(): mixed
    {
        return $this->model::with('shipping', 'user', 'carts')->where('user_id', Auth::id())->paginate(10);
    }

    /**
     * Search orders based on given data.
     *
     * @param  array<string, mixed>  $data
     * @return Builder|LengthAwarePaginator
     */
    public function search(array $data): mixed
    {
        $query = $this->model::query();

        $searchableFields = [
            'first_name',
            'last_name',
            'address1',
            'address2',
            'coupon',
            'phone',
            'post_code',
            'email',
        ];

        foreach ($searchableFields as $field) {
            if (Arr::has($data, $field)) {
                $query->where($field, 'like', '%'.Arr::get($data, $field).'%');
            }
        }

        if (Arr::has($data, 'all_included') && (bool)Arr::get($data, 'all_included') === true || empty($data)) {
            $query->with('shipping', 'user', 'carts')->get();
        } else {
            $orderBy = Arr::get($data, 'order_by', 'id');
            $sort = Arr::get($data, 'sort', 'desc');

            $query->orderBy($orderBy, $sort);

            $perPage = Arr::get($data, 'per_page', (new Order())->getPerPage());

            $query->with('shipping', 'user', 'carts')->paginate($perPage);
        }

        return $query;
    }

    public function findAll(): Collection
    {
        return $this->model::with('user', 'carts', 'shipping')->get();
    }
}
