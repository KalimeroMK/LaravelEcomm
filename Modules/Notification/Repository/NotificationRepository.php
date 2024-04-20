<?php

namespace Modules\Notification\Repository;

use Modules\Core\Repositories\Repository;
use Modules\Notification\Models\Notification;

class NotificationRepository extends Repository
{
    public \Illuminate\Database\Eloquent\Model $model = Notification::class;

    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::get();
    }

    /**
     * @param  string  $id
     *
     * @return mixed
     */
    public function getById(string $id): mixed
    {
        return $this->model::find($id);
    }
}