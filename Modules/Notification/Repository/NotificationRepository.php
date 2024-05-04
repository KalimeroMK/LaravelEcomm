<?php

namespace Modules\Notification\Repository;

use Modules\Core\Repositories\Repository;
use Modules\Notification\Models\Notification;

class NotificationRepository extends Repository
{
    public $model = Notification::class;

    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::get();
    }

    /**
     * @param  int  $id
     *
     * @return mixed
     */
    public function getById(int $id): mixed
    {
        return $this->model::find($id);
    }
}