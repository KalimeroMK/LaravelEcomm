<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
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
            $this->createAction->execute($dto);
            
            // Update order if order_id is provided
            if ($request->has('order_id')) {
                $order = Order::find($request->input('order_id'));
                if ($order) {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'processing',
                        'transaction_reference' => 'stripe_' . uniqid(),
                    ]);
                }
            }
            
            return $this
                ->setMessage('Payment successful')
                ->respond(['transaction_id' => 'stripe_' . uniqid()]);
                
        } catch (\Exception $e) {
            return $this
                ->setMessage('Payment failed: ' . $e->getMessage())
                ->setStatusCode(400)
                ->respond(null);
        }
    }
}
