<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Billing\Actions\Stripe\CreateStripeChargeAction;
use Modules\Billing\DTOs\StripeDTO;
use Modules\Core\Helpers\Payment;
use Modules\Core\Http\Controllers\CoreController;
use Session;

// theme_view() is loaded via composer autoload files

class StripeController extends CoreController
{
    private CreateStripeChargeAction $createAction;

    private Payment $payment;

    public function __construct(CreateStripeChargeAction $createAction, Payment $payment)
    {
        $this->createAction = $createAction;
        $this->payment = $payment;
    }

    /**
     * success response method.
     */
    public function stripe(int $id): View|Factory|Application
    {
        return view(theme_view('pages.stripe'), ['id' => $id]);
    }

    public function stripePost(Request $request): RedirectResponse
    {
        $dto = new StripeDTO(
            amount: $this->payment->calculate($request),
            currency: 'usd',
            source: $request->stripeToken,
            description: 'KalimeroMK E-comm'
        );
        $this->createAction->execute($dto);
        // orderSave logic here if needed
        Session::flash('success', 'Payment successful!');

        return redirect()->route('orders.index');
    }
}
