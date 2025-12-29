<?php

declare(strict_types=1);

namespace Modules\Category\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Category\Actions\CreateCategoryAction;
use Modules\Category\Actions\DeleteCategoryAction;
use Modules\Category\Actions\FindCategoryAction;
use Modules\Category\Actions\GetAllCategoriesAction;
use Modules\Category\Actions\GetCategoryTreeAction;
use Modules\Category\Actions\UpdateCategoryAction;
use Modules\Category\Actions\UpdateCategoryOrderAction;
use Modules\Category\DTOs\CategoryDTO;
use Modules\Category\Http\Requests\Api\Store;
use Modules\Category\Http\Requests\Api\Update;
use Modules\Category\Http\Resources\CategoryResource;
use Modules\Category\Models\Category;
use Modules\Category\Repository\CategoryRepository;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use ReflectionException;

class CategoryController extends CoreController
{
    public function __construct(
        private readonly CategoryRepository $repository,
        private readonly GetAllCategoriesAction $getAllCategoriesAction,
        private readonly GetCategoryTreeAction $getCategoryTreeAction,
        private readonly FindCategoryAction $findCategoryAction,
        private readonly CreateCategoryAction $createAction,
        private readonly UpdateCategoryAction $updateAction,
        private readonly DeleteCategoryAction $deleteAction,
        private readonly UpdateCategoryOrderAction $updateOrderAction
    ) {}

    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Category::class);

        $categories = $this->getAllCategoriesAction->execute();

        return CategoryResource::collection($categories);
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', Category::class);

        $dto = CategoryDTO::fromRequest($request);
        $category = $this->createAction->execute($dto);

        return $this
            ->setMessage(__('apiResponse.storeSuccess', [
                'resource' => Helper::getResourceName(Category::class),
            ]))
            ->respond(new CategoryResource($category));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->findCategoryAction->execute($id);
        $this->authorize('view', $category);

        return $this
            ->setMessage(__('apiResponse.ok', [
                'resource' => Helper::getResourceName(Category::class),
            ]))
            ->respond(new CategoryResource($category));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $this->authorizeFromRepo(CategoryRepository::class, 'update', $id);

        $dto = CategoryDTO::fromRequest($request, $id, $this->repository->findById($id));
        $category = $this->updateAction->execute($dto);

        return $this
            ->setMessage(__('apiResponse.updateSuccess', [
                'resource' => Helper::getResourceName(Category::class),
            ]))
            ->respond(new CategoryResource($category));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $category = $this->findCategoryAction->execute($id);
        $this->authorize('delete', $category);

        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', [
                'resource' => Helper::getResourceName(Category::class),
            ]))
            ->respond(null);
    }

    /**
     * Get category tree structure.
     */
    public function tree(): JsonResponse
    {
        $this->authorize('viewAny', Category::class);

        $tree = $this->getCategoryTreeAction->execute();

        return $this
            ->setMessage('Category tree retrieved successfully.')
            ->respond(CategoryResource::collection($tree));
    }

    /**
     * Update category order (for nested set drag-and-drop).
     */
    public function updateOrder(\Illuminate\Http\Request $request): JsonResponse
    {
        $this->authorize('update', Category::class);

        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.parent_id' => 'nullable|exists:categories,id',
            'categories.*.order' => 'sometimes|integer',
        ]);

        $this->updateOrderAction->execute($request->input('categories'));

        return $this
            ->setMessage('Category order updated successfully.')
            ->respond(null);
    }
}
