<?php

namespace Modules\Notification\Service;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Modules\Notification\Repository\NotificationRepository;

class NotificationService
{
    private NotificationRepository $notification_repository;
    
    public function __construct(NotificationRepository $notification_repository)
    {
        $this->notification_repository = $notification_repository;
    }
    
    /**
     * @param $id
     *
     * @return void
     */
    public function delete($id): void
    {
        $this->notification_repository->delete($id);
    }
    
    /**
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        return $this->notification_repository->findAll();
    }
}