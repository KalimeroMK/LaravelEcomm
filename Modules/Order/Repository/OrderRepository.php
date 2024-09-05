<?php

namespace Modules\Order\Repository;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\Repository;
use Modules\Order\Http\Resources\OrderResource;
use Modules\Order\Models\Order;

class OrderRepository extends Repository implements SearchInterface
{
    public $model = Order::class;

    public function findAllByUser(): mixed
    {
        return $this->model::with('shipping', 'user', 'carts')->where('user_id', Auth::id())->paginate(10);
    }

    public function search(array $data): LengthAwarePaginator
    {
        $query = $this->model::query();

        $searchableFields = [
            'order_number',
            'user_id',
            'sub_total',
            'shipping_id',
            'total_amount',
            'quantity',
            'payment_method',
            'payment_status',
            'status',
            'post_code',
        ];

        foreach ($searchableFields as $field) {
            if (Arr::has($data, $field)) {
                $query->whereLike($field, Arr::get($data, $field));
            }
        }

        if (Arr::has($data, 'name')) {
            $query->whereHas('user', fn ($q) => $q->whereLike('name', Arr::get($data, 'name')));
        }

        if (Arr::has($data, 'email')) {
            $query->whereHas('user', fn ($q) => $q->whereLike('email', Arr::get($data, 'email')));
        }

        $query->with('shipping', 'user', 'carts');

        if (! Arr::get($data, 'all_included', false) && ! empty($data)) {
            $query->orderBy(
                Arr::get($data, 'order_by', 'id'),
                Arr::get($data, 'sort', 'desc')
            );
        }

        $results = $query->paginate(Arr::get($data, 'per_page', (new Order)->getPerPage()));

        $results->getCollection()->transform(fn ($item) => new OrderResource($item));

        return $results;
    }

    public function findAll(): Collection
    {
        return $this->model::with('user', 'carts', 'shipping')->get();
    }
}
