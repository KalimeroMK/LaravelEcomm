<?php

declare(strict_types=1);

namespace Modules\Cart\Repository;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\Cart\Models\Cart;
use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Repositories\EloquentRepository;

class CartRepository extends EloquentRepository implements EloquentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(Cart::class);
    }

    /**
     * Get all cart entries.
     */
    public function findAll(): Collection
    {
        return (new $this->modelClass)->get();
    }

    /**
     * Get cart entries for the currently authenticated user.
     */
    public function show(): Collection
    {
        return (new $this->modelClass)
            ->where('user_id', Auth::id())
            ->get();
    }
}
