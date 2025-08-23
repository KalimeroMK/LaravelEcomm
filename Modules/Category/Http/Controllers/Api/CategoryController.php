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
use Modules\Category\Repository\CategoryRepository;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use ReflectionException;

class CategoryController extends CoreController
{
    public function __construct(
        private readonly CategoryRepository $repository,
        private readonly CreateCategoryAction $createAction,
        private readonly UpdateCategoryAction $updateAction,
        private readonly DeleteCategoryAction $deleteAction
    ) {}

    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Category::class);

        return CategoryResource::collection($this->repository->findAll());
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
        $category = $this->authorizeFromRepo(CategoryRepository::class, 'view', $id);

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
        $this->authorizeFromRepo(CategoryRepository::class, 'delete', $id);

        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', [
                'resource' => Helper::getResourceName(Category::class),
            ]))
            ->respond(null);
    }
}
