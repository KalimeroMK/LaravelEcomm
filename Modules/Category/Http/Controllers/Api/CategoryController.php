<?php

namespace Modules\Category\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Category\Http\Requests\Api\Store;
use Modules\Category\Http\Requests\Api\Update;
use Modules\Category\Service\CategoryService;

class CategoryController extends Controller
{
    private CategoryService $category_service;
    
    public function __construct(CategoryService $category_service)
    {
        $this->category_service = $category_service;
    }
    
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->sendResponse([$this->category_service->getAll()], 200);
    }
    
    /**
     * @param  Store  $request
     *
     * @return JsonResponse
     */
    public function store(Store $request): JsonResponse
    {
        return $this->sendResponse([$this->category_service->store($request->all())], 200);
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        return $this->sendResponse([$this->category_service->edit($id)], 200);
    }
    
    /**
     * @param  Update  $request
     * @param $id
     *
     * @return JsonResponse
     */
    public function update(Update $request, $id): JsonResponse
    {
        return $this->sendResponse([$this->category_service->update($id, $request->all())], 200);
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        return $this->sendResponse([$this->category_service->destroy($id)], 200);
    }
}
