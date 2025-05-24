<?php

declare(strict_types=1);

namespace Modules\Notification\Repository;

use Illuminate\Support\Collection;
use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Repositories\EloquentRepository;
use Modules\Notification\Models\Notification;

class NotificationRepository extends EloquentRepository implements EloquentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(Notification::class);
    }

    /**
     * Get all notifications.
     */
    public function findAll(): Collection
    {
        return (new $this->modelClass)->get();
    }

    /**
     * Get a notification by ID.
     */
    public function getById(int $id): ?Notification
    {
        return (new $this->modelClass)->find($id);
    }
}
