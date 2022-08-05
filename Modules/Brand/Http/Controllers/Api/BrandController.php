<?php

namespace Modules\Brand\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Brand\Http\Requests\Api\Store;
use Modules\Brand\Http\Requests\Api\Update;
use Modules\Brand\Service\BrandService;

class BrandController extends Controller
{
    private BrandService $brand_service;
    
    public function __construct(BrandService $brand_service)
    {
        $this->brand_service = $brand_service;
    }
    
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->sendResponse([$this->brand_service->getAll()], 200);
    }
    
    /**
     * @param  Store  $request
     *
     * @return JsonResponse
     */
    public function store(Store $request): JsonResponse
    {
        return $this->sendResponse([$this->brand_service->store($request)], 200);
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        return $this->sendResponse([$this->brand_service->edit($id)], 200);
    }
    
    /**
     * @param  Update  $request
     * @param $id
     *
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        return $this->sendResponse([$this->brand_service->update($request->all(), $id)], 200);
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        return $this->sendResponse([$this->brand_service->destroy($id)], 200);
    }
}
