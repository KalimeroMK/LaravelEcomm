<?php

namespace Modules\Notification\Service;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
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
     * @return RedirectResponse
     */
    public function delete($id): RedirectResponse
    {
        $notification = $this->notification_repository->getById($id);
        if ($notification) {
            $status = $notification->delete();
            if ($status) {
                request()->session()->flash('success', 'Notification successfully deleted');
            } else {
                request()->session()->flash('error', 'Error please try again');
            }
        } else {
            request()->session()->flash('error', 'Notification not found');
        }
        
        return back();
    }
    
    /**
     * @param  Request  $request
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function show(Request $request): Redirector|RedirectResponse|Application
    {
        $notification = Auth()->user()->notifications()->where('id', $request->id)->first();
        if ($notification) {
            $notification->markAsRead();
            
            return redirect($notification->data['actionURL']);
        }
    }
    
    /**
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        $notification = $this->notification_repository->getAll();
        
        return view('notification::index', compact('notification'));
    }
}