<?php

namespace Modules\Newsletter\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Newsletter\Http\Requests\Api\Store;
use Modules\Newsletter\Service\NewsletterService;

class NewsletterController extends Controller
{
    
    private NewsletterService $newsletter_service;
    
    public function __construct(NewsletterService $newsletter_service)
    {
        $this->newsletter_service = $newsletter_service;
    }
    
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->sendResponse([$this->newsletter_service->getAll()], 200);
    }
    
    /**
     * @param  Store  $request
     *
     * @return JsonResponse
     */
    public function store(Store $request): JsonResponse
    {
        return $this->sendResponse([$this->newsletter_service->store($request->all())], 200);
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        return $this->sendResponse([$this->newsletter_service->edit($id)], 200);
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        return $this->sendResponse([$this->newsletter_service->destroy($id)], 200);
    }
}
