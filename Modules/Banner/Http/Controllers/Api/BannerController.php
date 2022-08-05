<?php

namespace Modules\Banner\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Banner\Http\Requests\Api\Store;
use Modules\Banner\Http\Requests\Api\Update;
use Modules\Banner\Service\BannerService;

class BannerController extends Controller
{
    private BannerService $banner_service;
    
    public function __construct(BannerService $banner_service)
    {
        $this->banner_service = $banner_service;
    }
    
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->sendResponse([$this->banner_service->getAll()], 200);
    }
    
    /**
     * @param  Store  $request
     *
     * @return JsonResponse
     */
    public function store(Store $request): JsonResponse
    {
        return $this->sendResponse([$this->banner_service->store($request)], 200);
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        return $this->sendResponse([$this->banner_service->show($id)], 200);
    }
    
    /**
     * @param  Update  $request
     * @param $id
     *
     * @return JsonResponse
     */
    public function update(Update $request, $id): JsonResponse
    {
        return $this->sendResponse([$this->banner_service->update($request->all(), $id)], 200);
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        return $this->sendResponse([$this->banner_service->destroy($id)], 200);
    }
}
