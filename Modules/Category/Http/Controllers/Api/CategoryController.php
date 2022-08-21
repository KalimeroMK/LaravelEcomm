<?php

namespace Modules\Category\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Category\Http\Requests\Api\Store;
use Modules\Category\Http\Requests\Api\Update;
use Modules\Category\Http\Resources\CategoryResource;
use Modules\Category\Service\CategoryService;
use Modules\Core\Helpers\Helper;
use Modules\Core\Traits\ApiResponses;

class CategoryController extends Controller
{
    use ApiResponses;
    
    private CategoryService $category_service;
    
    public function __construct(CategoryService $category_service)
    {
        $this->category_service = $category_service;
    }
    
    /**
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return CategoryResource::collection($this->category_service->getAll());
    }
    
    /**
     * @param  Store  $request
     *
     * @return JsonResponse|string
     */
    public function store(Store $request)
    {
        try {
            return $this
                ->setMessage(
                    __(
                        'apiResponse.storeSuccess',
                        [
                            'resource' => Helper::getResourceName(
                                $this->category_service->category_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new CategoryResource($this->category_service->store($request->validated())));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse|string
     */
    public function show($id)
    {
        try {
            return $this
                ->setMessage(
                    __(
                        'apiResponse.ok',
                        [
                            'resource' => Helper::getResourceName(
                                $this->category_service->category_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new CategoryResource($this->category_service->show($id)));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param  Update  $request
     * @param $id
     *
     * @return JsonResponse|string
     */
    public function update(Update $request, $id)
    {
        try {
            return $this
                ->setMessage(
                    __(
                        'apiResponse.updateSuccess',
                        [
                            'resource' => Helper::getResourceName(
                                $this->category_service->category_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new CategoryResource($this->category_service->update($id, $request->all())));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse|string
     */
    public function destroy($id)
    {
        try {
            return $this
                ->setMessage(
                    __(
                        'apiResponse.deleteSuccess',
                        [
                            'resource' => Helper::getResourceName(
                                $this->category_service->category_repository->model
                            ),
                        ]
                    )
                )
                ->respond($this->category_service->destroy($id));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}
