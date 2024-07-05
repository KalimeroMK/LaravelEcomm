<?php

namespace Modules\Billing\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Billing\Service\StripeService;
use Modules\Core\Http\Controllers\CoreController;
use Stripe\Exception\ApiErrorException;

class StripeController extends CoreController
{
    private StripeService $stripe_service;

    public function __construct(StripeService $stripe_service)
    {
        $this->stripe_service = $stripe_service;
    }

    /**
     * success response method.
     */
    public function stripe(int $id): View|Factory|Application
    {
        return $this->stripe_service->stripe($id);
    }

    /**
     * success response method.
     *
     *
     * @throws ApiErrorException
     */
    public function stripePost(Request $request): RedirectResponse
    {
        return $this->stripe_service->stripePost($request);
    }
}
