<?php

namespace Modules\Core\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class StatusNotification extends Notification
{
    use Queueable;

    /**
     * The details of the notification.
     *
     * @var array<string, mixed>
     */
    private array $details;

    /**
     * Create a new notification instance.
     *
     * @param  array<string, mixed>  $details  An associative array containing details like 'title' and 'actionURL'.
     */
    public function __construct(array $details)
    {
        $this->details = $details;
    }

    /**
     * Get the notification delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array<string> The delivery channels.
     */
    public function via(mixed $notifiable): array
    {
        return ['database', 'broadcast'];
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array<string, mixed> An array containing notification data.
     */
    public function toArray(mixed $notifiable): array
    {
        return [
            'title' => $this->details['title'],
            'actionURL' => $this->details['actionURL'],
            'fas' => $this->details['fas'],
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     *
     * @param  mixed  $notifiable
     *
     * @return BroadcastMessage
     */
    public function toBroadcast(mixed $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => $this->details['title'],
            'actionURL' => $this->details['actionURL'],
            'url' => route('admin.notification', $this->id),
            'fas' => $this->details['fas'],
            'time' => now()->format('F d, Y h:i A'),
        ]);
    }
}
