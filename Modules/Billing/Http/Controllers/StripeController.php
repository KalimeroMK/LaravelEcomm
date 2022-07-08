<?php

namespace Modules\Billing\Http\Controllers;

use App\Helpers\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\Pure;
use Modules\Billing\Service\StripeService;
use Stripe\Exception\ApiErrorException;

class StripeController extends Controller
{
    public Payment $payment;
    private StripeService $stripe_service;
    
    #[Pure] public function __construct()
    {
        $this->payment        = new Payment();
        $this->stripe_service = new StripeService($this);
    }
    
    /**
     * success response method.
     *
     * @param $id
     *
     * @return Application|Factory|View
     */
    
    public function stripe($id): View|Factory|Application
    {
        return $this->stripe_service->stripe($id);
    }
    
    /**
     * success response method.
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     * @throws ApiErrorException
     */
    
    public function stripePost(Request $request): RedirectResponse
    {
        return $this->stripe_service->stripePost($request);
    }
}
