<?php

namespace Modules\Notification\Service;

use Modules\Notification\Repository\NotificationRepository;

class NotificationService
{
    private NotificationRepository $notification_repository;

    public function __construct(NotificationRepository $notification_repository)
    {
        $this->notification_repository = $notification_repository;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->notification_repository->delete($id);
    }

    /**
     * @return mixed|string
     */
    public function getAll(): mixed
    {
        return $this->notification_repository->findAll();
    }

    /**
     * @param  int  $id
     * @return mixed
     */
    public function getById(int $id): mixed
    {
        return $this->notification_repository->getById($id);
    }
}
