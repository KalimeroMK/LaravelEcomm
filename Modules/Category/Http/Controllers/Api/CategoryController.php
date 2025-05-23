<?php

declare(strict_types=1);

namespace Modules\Category\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Category\Actions\CreateCategoryAction;
use Modules\Category\Actions\DeleteCategoryAction;
use Modules\Category\Actions\UpdateCategoryAction;
use Modules\Category\DTOs\CategoryDTO;
use Modules\Category\Http\Requests\Api\Store;
use Modules\Category\Http\Requests\Api\Update;
use Modules\Category\Http\Resources\CategoryResource;
use Modules\Category\Models\Category;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;

class CategoryController extends CoreController
{
    private CreateCategoryAction $createAction;

    private UpdateCategoryAction $updateAction;

    private DeleteCategoryAction $deleteAction;

    public function __construct(
        CreateCategoryAction $createAction,
        UpdateCategoryAction $updateAction,
        DeleteCategoryAction $deleteAction
    ) {
        $this->createAction = $createAction;
        $this->updateAction = $updateAction;
        $this->deleteAction = $deleteAction;
        $this->middleware('permission:category-list', ['only' => ['index']]);
        $this->middleware('permission:category-show', ['only' => ['show']]);
        $this->middleware('permission:category-create', ['only' => ['store']]);
        $this->middleware('permission:category-edit', ['only' => ['update']]);
        $this->middleware('permission:category-delete', ['only' => ['destroy']]);
    }

    public function index(): ResourceCollection
    {
        return CategoryResource::collection(Category::all());
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        $dto = CategoryDTO::fromRequest($request);
        $category = $this->createAction->execute($dto);

        return $this
            ->setMessage(
                __(
                    'apiResponse.storeSuccess',
                    [
                        'resource' => Helper::getResourceName(Category::class),
                    ]
                )
            )
            ->respond(new CategoryResource($category));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        return $this
            ->setMessage(
                __(
                    'apiResponse.ok',
                    [
                        'resource' => Helper::getResourceName(Category::class),
                    ]
                )
            )
            ->respond(new CategoryResource($category));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $dto = CategoryDTO::fromRequest($request, $id);
        $category = $this->updateAction->execute($dto);

        return $this
            ->setMessage(
                __(
                    'apiResponse.updateSuccess',
                    [
                        'resource' => Helper::getResourceName(Category::class),
                    ]
                )
            )
            ->respond(new CategoryResource($category));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->deleteAction->execute($id);

        return $this
            ->setMessage(
                __(
                    'apiResponse.deleteSuccess',
                    [
                        'resource' => Helper::getResourceName(Category::class),
                    ]
                )
            )
            ->respond(null);
    }
}
