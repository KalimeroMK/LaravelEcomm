<?php

namespace Modules\Banner\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Banner\Http\Requests\Api\StoreRequest;
use Modules\Banner\Http\Requests\Api\UpdateRequest;
use Modules\Banner\Models\Banner;
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
     * @param  StoreRequest  $request
     *
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
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
     * @param  UpdateRequest  $request
     * @param $id
     *
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, $id): JsonResponse
    {
        $banner = Banner::findOrFail($id);
        $data   = $request;
        
        return $this->sendResponse([$this->banner_service->update($data, $banner->id)], 200);
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
