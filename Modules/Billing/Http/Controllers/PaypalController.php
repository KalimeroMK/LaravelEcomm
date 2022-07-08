<?php

namespace Modules\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use JetBrains\PhpStorm\NoReturn;
use Modules\Billing\Service\PaypalService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class PaypalController extends Controller
{
    private PaypalService $paypal_service;
    
    public function __construct() { $this->paypal_service = new PaypalService(); }
    
    /**
     * @return Application|RedirectResponse|Redirector
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception|Throwable
     */
    public function payment(): Redirector|RedirectResponse|Application
    {
        return $this->paypal_service->payment();
    }
    
    /**
     * Responds with a welcome message with instructions
     *
     * @return Response
     */
    #[NoReturn] public function cancel(): Response
    {
        return $this->paypal_service->cancel();
    }
    
    /**
     * Responds with a welcome message with instructions
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     * @throws Exception|Throwable
     */
    public function success(Request $request): RedirectResponse
    {
        return $this->paypal_service->success($request);
    }
}
