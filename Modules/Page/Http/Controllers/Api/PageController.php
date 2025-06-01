<?php

declare(strict_types=1);

namespace Modules\Page\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Page\Actions\CreatePageAction;
use Modules\Page\Actions\DeletePageAction;
use Modules\Page\Actions\GetAllPagesAction;
use Modules\Page\Actions\UpdatePageAction;
use Modules\Page\DTOs\PageDTO;
use Modules\Page\Http\Requests\Api\Store;
use Modules\Page\Http\Requests\Api\Update;
use Modules\Page\Http\Resources\PageResource;
use Modules\Page\Models\Page;
use Modules\Page\Repository\PageRepository;
use ReflectionException;

class PageController extends CoreController
{
    public function __construct(
        private readonly PageRepository $repository,
        private readonly GetAllPagesAction $getAllPagesAction,
        private readonly CreatePageAction $createAction,
        private readonly UpdatePageAction $updateAction,
        private readonly DeletePageAction $deleteAction
    ) {}

    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Page::class);

        return PageResource::collection($this->repository->all());
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', Page::class);

        $dto = PageDTO::fromRequest($request);
        $page = $this->createAction->execute($dto);

        return $this
            ->setMessage(__('apiResponse.storeSuccess', [
                'resource' => Helper::getResourceName(Page::class),
            ]))
            ->respond(new PageResource($page));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $page = $this->authorizeFromRepo(PageRepository::class, 'view', $id);

        return $this
            ->setMessage(__('apiResponse.ok', [
                'resource' => Helper::getResourceName(Page::class),
            ]))
            ->respond(new PageResource($page));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $this->authorizeFromRepo(PageRepository::class, 'update', $id);

        $dto = PageDTO::fromRequest($request, $id, $this->repository->findById($id));
        $page = $this->updateAction->execute($dto);

        return $this
            ->setMessage(__('apiResponse.updateSuccess', [
                'resource' => Helper::getResourceName(Page::class),
            ]))
            ->respond(new PageResource($page));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->authorizeFromRepo(PageRepository::class, 'delete', $id);

        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', [
                'resource' => Helper::getResourceName(Page::class),
            ]))
            ->respond(null);
    }
}
