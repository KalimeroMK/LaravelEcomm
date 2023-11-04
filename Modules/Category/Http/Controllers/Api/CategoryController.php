<?php

namespace Modules\Category\Http\Controllers\Api;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Category\Http\Requests\Api\Store;
use Modules\Category\Http\Requests\Api\Update;
use Modules\Category\Http\Resources\CategoryResource;
use Modules\Category\Models\Category;
use Modules\Category\Service\CategoryService;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;

class CategoryController extends CoreController
{

    private CategoryService $category_service;

    public function __construct(CategoryService $category_service)
    {
        $this->category_service = $category_service;
        $this->authorizeResource(Category::class);
    }

    public function index(): ResourceCollection
    {
        return CategoryResource::collection($this->category_service->getAll());
    }

    public function store(Store $request)
    {
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
    }

    public function show($id)
    {
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
    }

    public function update(Update $request, $id)
    {
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
    }

    public function destroy($id)
    {
        $this->category_service->destroy($id);
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
            ->respond(null);
    }
}
