<?php

declare(strict_types=1);

namespace Modules\Order\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Order\Models\Order;

/**
 * Order confirmation sent to the customer right after checkout.
 */
class OrderPlacedNotification extends Notification implements ShouldQueue
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
            ->subject(__('Order confirmation :number', ['number' => $order->order_number]))
            ->greeting(__('Thank you for your order, :name!', ['name' => trim(($order->first_name ?? '').' '.($order->last_name ?? '')) ?: ($order->user?->name ?? '')]))
            ->line(__('We received your order :number and it is now being processed.', ['number' => $order->order_number]));

        foreach ($order->carts as $line) {
            $mail->line(sprintf(
                '%d × %s — %s',
                $line->quantity,
                $line->product?->title ?? __('Product'),
                number_format((float) $line->amount, 2)
            ));
        }

        $mail->line(__('Subtotal: :amount', ['amount' => number_format((float) $order->sub_total, 2)]))
            ->line(__('Total: :amount', ['amount' => number_format((float) $order->total_amount, 2)]))
            ->line(__('Payment method: :method', ['method' => mb_strtoupper((string) $order->payment_method)]));

        if ($order->user_id) {
            $mail->action(__('View your order'), route('user.orders.detail', $order));
        }

        return $mail->line(__('We will let you know as soon as it ships.'));
    }
}
