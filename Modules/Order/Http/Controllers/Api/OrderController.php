<?php

namespace Modules\Order\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Order\Http\Requests\Api\Store;
use Modules\Order\Http\Requests\Api\Update;
use Modules\Order\Service\OrderService;

class OrderController extends Controller
{
    
    private OrderService $order_service;
    
    public function __construct(OrderService $order_service)
    {
        $this->order_service = $order_service;
    }
    
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->sendResponse([$this->order_service->getAll()], 200);
    }
    
    /**
     * @param  Store  $request
     *
     * @return JsonResponse
     */
    public function store(Store $request): JsonResponse
    {
        return $this->sendResponse([$this->order_service->store($request->all())], 200);
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        return $this->sendResponse([$this->order_service->edit($id)], 200);
    }
    
    /**
     * @param  Update  $request
     * @param $id
     *
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        return $this->sendResponse([$this->order_service->update($request->all(), $id)], 200);
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        return $this->sendResponse([$this->order_service->destroy($id)], 200);
    }
}
