<?php

declare(strict_types=1);

namespace Modules\Order\Repository;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\EloquentRepository;
use Modules\Order\Http\Resources\OrderResource;
use Modules\Order\Models\Order;

class OrderRepository extends EloquentRepository implements EloquentRepositoryInterface, SearchInterface
{
    public function __construct()
    {
        parent::__construct(Order::class);
    }

    /**
     * Get all orders with related user, carts, and shipping info.
     */
    public function findAll(): Collection
    {
        return (new $this->modelClass)->with('user', 'carts', 'shipping')->get();
    }

    /**
     * Get all orders for the authenticated user.
     */
    public function findAllByUser($userId): LengthAwarePaginator
    {
        return (new $this->modelClass)
            ->with('shipping', 'user', 'carts')
            ->where('user_id', $userId)
            ->paginate(10);
    }

    /**
     * Search orders with filters and relationships.
     *
     * @param  array<string, mixed>  $data
     * @return LengthAwarePaginator
     */
    public function search(array $data): LengthAwarePaginator
    {
        $query = (new $this->modelClass)->newQuery();

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
            $query->whereHas('user', fn($q) => $q->whereLike('name', Arr::get($data, 'name')));
        }

        if (Arr::has($data, 'email')) {
            $query->whereHas('user', fn($q) => $q->whereLike('email', Arr::get($data, 'email')));
        }

        $query->with('shipping', 'user', 'carts');

        if (!Arr::get($data, 'all_included', false) && $data !== []) {
            $query->orderBy(
                Arr::get($data, 'order_by', 'id'),
                Arr::get($data, 'sort', 'desc')
            );
        }

        $results = $query->paginate(Arr::get($data, 'per_page', (new $this->modelClass)->getPerPage()));

        $results->getCollection()->transform(fn($item): OrderResource => new OrderResource($item));

        return $results;
    }
}
