<?php

namespace Modules\Order\Http\Controllers;

use App\Helpers\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Order\Service\OrderFrontService;

class OrderFrontController extends Controller
{
    public Payment $payment;
    private OrderFrontService $order_front_service;
    
    public function __construct(OrderFrontService $order_front_service)
    {
        $this->payment             = new Payment();
        $this->order_front_service = $order_front_service;
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        return $this->order_front_service->store($request);
    }
    /**
     * @param  Request  $request
     *
     * @return Response
     */
    // PDF generate
    public function pdf(Request $request): Response
    {
        return $this->order_front_service->pdf($request);
    }
    /**
     * @param  Request  $request
     *
     * @return array
     */
    // Income chart
    public function incomeChart(Request $request): array
    {
        return $this->order_front_service->incomeChart($request);
    }
}
