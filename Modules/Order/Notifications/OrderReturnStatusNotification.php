<?php

declare(strict_types=1);

namespace Modules\Order\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Order\Models\OrderReturn;

/**
 * Sent to the customer whenever their return request changes state
 * (received, approved, rejected, refunded).
 */
class OrderReturnStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly OrderReturn $orderReturn) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $return = $this->orderReturn;
        $order = $return->order;

        $mail = (new MailMessage)
            ->subject(__('Return request for order :number: :status', [
                'number' => $order->order_number,
                'status' => __(ucfirst($return->status)),
            ]));

        $mail->line(match ($return->status) {
            OrderReturn::STATUS_REQUESTED => __('We received your return request for order :number and will review it shortly.', ['number' => $order->order_number]),
            OrderReturn::STATUS_APPROVED => __('Your return for order :number was approved. Please send the items back; the refund follows once they arrive.', ['number' => $order->order_number]),
            OrderReturn::STATUS_REJECTED => __('Unfortunately your return request for order :number was declined.', ['number' => $order->order_number]),
            OrderReturn::STATUS_REFUNDED => __('Your refund of :amount for order :number has been issued.', [
                'amount' => number_format((float) ($return->refund_amount ?? $order->total_amount), 2),
                'number' => $order->order_number,
            ]),
            default => __('The status of your return request for order :number changed to :status.', [
                'number' => $order->order_number,
                'status' => $return->status,
            ]),
        });

        if ($return->admin_note) {
            $mail->line(__('Note: :note', ['note' => $return->admin_note]));
        }

        if ($order->user_id) {
            $mail->action(__('View your order'), route('user.orders.detail', $order));
        }

        return $mail;
    }
}
