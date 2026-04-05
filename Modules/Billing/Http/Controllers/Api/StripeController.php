<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Billing\Actions\Stripe\CreateStripeChargeAction;
use Modules\Billing\DTOs\StripeDTO;
use Modules\Billing\Http\Requests\Api\Stripe as StripeData;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Order\Models\Order;

class StripeController extends CoreController
{
    private CreateStripeChargeAction $createAction;

    public function __construct(CreateStripeChargeAction $createAction)
    {
        $this->middleware('auth:sanctum');
        $this->createAction = $createAction;
    }

    /**
     * success response method.
     */
    public function stripe(StripeData $request): JsonResponse
    {
        $dto = StripeDTO::fromRequest($request);

        try {
            $transactionId = DB::transaction(function () use ($dto, $request): string {
                // Execute charge first — throws on failure
                $this->createAction->execute($dto);

                $txId = 'stripe_'.uniqid();

                if ($request->has('order_id')) {
                    $order = Order::lockForUpdate()->find($request->input('order_id'));
                    if ($order) {
                        if ($order->payment_status === 'paid') {
                            throw new \RuntimeException('Order already paid');
                        }
                        $order->update([
                            'payment_status'        => 'paid',
                            'status'                => 'processing',
                            'transaction_reference' => $txId,
                        ]);
                    }
                }

                return $txId;
            });

            return $this
                ->setMessage('Payment successful')
                ->respond(['transaction_id' => $transactionId]);

        } catch (\Exception $e) {
            return $this
                ->setMessage('Payment failed: '.$e->getMessage())
                ->setStatusCode(400)
                ->respond(null);
        }
    }
}
