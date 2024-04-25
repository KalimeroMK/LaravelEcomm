<?php

namespace Modules\Category\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Brand\Http\Resource\BrandResource;
use Modules\Category\Http\Requests\Api\Store;
use Modules\Category\Http\Requests\Api\Update;
use Modules\Category\Http\Resources\CategoryResource;
use Modules\Category\Models\Category;
use Modules\Category\Service\CategoryService;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use ReflectionException;

class CategoryController extends CoreController
{

    public CategoryService $category_service;

    public function __construct(CategoryService $category_service)
    {
        $this->category_service = $category_service;
        $this->authorizeResource(Category::class, 'category');
    }

    public function index(): ResourceCollection
    {
        return BrandResource::collection($this->category_service->getAll());
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.storeSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->category_service->categoryRepository->model
                        ),
                    ]
                )
            )
            ->respond(new CategoryResource($this->category_service->store($request->validated())));
    }

    /**
     * @throws ReflectionException
     */
    public function show(Category $category): JsonResponse
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.ok',
                    [
                        'resource' => Helper::getResourceName(
                            $this->category_service->categoryRepository->model
                        ),
                    ]
                )
            )
            ->respond(new CategoryResource($this->category_service->show($category->id)));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, Category $category): JsonResponse
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.updateSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->category_service->categoryRepository->model
                        ),
                    ]
                )
            )
            ->respond(new CategoryResource($this->category_service->update($category->id, $request->validated())));
    }

    /**
     * @param  int  $id
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function destroy(int $id)
    {
        $this->category_service->destroy($id);

        $resourceName = Helper::getResourceName(
            $this->category_service->categoryRepository->model
        );

        $message = __('apiResponse.deleteSuccess', ['resource' => $resourceName]);

        // Assuming you have a method to set response message and status
        return $this->setMessage($message)->respond(null);
    }
}
