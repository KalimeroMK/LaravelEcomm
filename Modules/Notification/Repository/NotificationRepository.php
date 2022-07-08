<?php

namespace Modules\Notification\Repository;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use LaravelIdea\Helper\Modules\Message\Models\_IH_Message_C;
use Modules\Message\Models\Message;
use Modules\Notification\Models\Notification;

class NotificationRepository
{
    public function getAll(): LengthAwarePaginator|array|_IH_Message_C|\Illuminate\Pagination\LengthAwarePaginator
    {
        return Notification::orderBy('id', 'DESC')->paginate(10);
    }
    
    public function getById($id): Model|Collection|array|_IH_Message_C|Message
    {
        return Notification::findOrFail($id);
    }
}