<?php

namespace Modules\Message\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Message\Service\MessageService;

class MessageController extends Controller
{
    private MessageService $message_service;
    
    public function __construct(MessageService $message_service)
    {
        $this->message_service = $message_service;
    }
    
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->sendResponse([$this->message_service->getAll()], 200);
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        return $this->sendResponse([$this->message_service->show($id)], 200);
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        return $this->sendResponse([$this->message_service->destroy($id)], 200);
    }
}
