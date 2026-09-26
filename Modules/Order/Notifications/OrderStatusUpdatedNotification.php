<?php

declare(strict_types=1);

namespace Modules\Order\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Order\Models\Order;

/**
 * Sent to the customer whenever the status of their order changes
 * (processing, shipped with tracking details, delivered, cancelled...).
 */
class OrderStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Order $order) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->order;

        $mail = (new MailMessage)
            ->subject(__('Your order :number is now :status', [
                'number' => $order->order_number,
                'status' => __(ucfirst((string) $order->status)),
            ]))
            ->line(__('The status of your order :number changed to :status.', [
                'number' => $order->order_number,
                'status' => __(ucfirst((string) $order->status)),
            ]));

        if ($order->status === 'shipped' && $order->tracking_number) {
            $mail->line(__('Tracking number: :number:carrier', [
                'number' => $order->tracking_number,
                'carrier' => $order->tracking_carrier ? ' ('.$order->tracking_carrier.')' : '',
            ]));
        }

        if ($order->user_id) {
            $mail->action(__('Track your order'), route('user.orders.track', $order));
        }

        return $mail;
    }
}
