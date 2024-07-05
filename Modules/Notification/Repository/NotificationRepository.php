<?php

namespace Modules\Notification\Repository;

use Illuminate\Support\Collection;
use Modules\Core\Repositories\Repository;
use Modules\Notification\Models\Notification;

class NotificationRepository extends Repository
{
    public $model = Notification::class;

    public function findAll(): Collection
    {
        return $this->model::get();
    }

    public function getById(int $id): mixed
    {
        return $this->model::find($id);
    }
}
