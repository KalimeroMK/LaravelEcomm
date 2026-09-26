<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Order\Models\OrderReturn;
use Modules\Order\Notifications\OrderReturnStatusNotification;

/**
 * Execute a refund for an approved return: refund through the payment
 * gateway when possible, put the stock back, and flag the order refunded.
 */
class RefundOrderReturnAction
{
    /**
     * @return array{return: OrderReturn, gateway_refunded: bool}
     */
    public function execute(OrderReturn $orderReturn, ?float $amount = null, ?string $note = null): array
    {
        $order = $orderReturn->order;
        $amount ??= (float) $order->total_amount;

        // Stripe charges (ch_...) and payment intents (pi_...) can be
        // refunded through the API; COD/PayPal-legacy orders are manual.
        $gatewayReference = null;
        if ($order->payment_method === 'stripe'
            && $order->transaction_reference
            && (str_starts_with($order->transaction_reference, 'ch_') || str_starts_with($order->transaction_reference, 'pi_'))) {
            $gatewayReference = $this->refundViaStripe($order->transaction_reference, $amount);
        }

        DB::transaction(function () use ($orderReturn, $order, $amount, $note, $gatewayReference): void {
            $order->restoreStock();
            $order->update([
                'status' => 'refunded',
                'payment_status' => 'refunded',
            ]);

            $orderReturn->update([
                'status' => OrderReturn::STATUS_REFUNDED,
                'refund_amount' => $amount,
                'refund_reference' => $gatewayReference,
                'admin_note' => $note ?? $orderReturn->admin_note,
                'refunded_at' => now(),
            ]);
        });

        $order->notifyCustomer(new OrderReturnStatusNotification($orderReturn->refresh()));

        return [
            'return' => $orderReturn,
            'gateway_refunded' => $gatewayReference !== null,
        ];
    }

    private function refundViaStripe(string $reference, float $amount): ?string
    {
        try {
            \Stripe\Stripe::setApiKey(config('stripe.'.config('stripe.mode', 'sandbox').'.client_secret'));

            $payload = ['amount' => (int) round($amount * 100)];
            $payload[str_starts_with($reference, 'pi_') ? 'payment_intent' : 'charge'] = $reference;

            return \Stripe\Refund::create($payload)->id;
        } catch (\Throwable $e) {
            // A failed gateway refund must not block the manual flow -
            // the admin sees "manual refund required" in the result.
            Log::error('Stripe refund failed: '.$e->getMessage(), ['reference' => $reference]);

            return null;
        }
    }
}
