<?php

namespace Modules\Order\Repository;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use LaravelIdea\Helper\Modules\Message\Models\_IH_Message_C;
use Modules\Message\Models\Message;
use Modules\Order\Models\Order;

class OrderRepository
{
    public function getAll(): LengthAwarePaginator|array|_IH_Message_C|\Illuminate\Pagination\LengthAwarePaginator
    {
        return Order::with('shipping')->orderBy('id', 'DESC')->paginate(10);
    }
    
    public function getById($id): Model|Collection|array|_IH_Message_C|Message
    {
        return Order::findOrFail($id);
    }
}