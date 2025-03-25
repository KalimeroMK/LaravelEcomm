<?php

declare(strict_types=1);

namespace Modules\Page\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Page\Http\Requests\Api\Store;
use Modules\Page\Http\Requests\Api\Update;
use Modules\Page\Http\Resources\PageResource;
use Modules\Page\Service\PageService;
use ReflectionException;

class PageController extends CoreController
{
    private PageService $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
        $this->middleware('permission:page-list', ['only' => ['index']]);
        $this->middleware('permission:page-show', ['only' => ['show']]);
        $this->middleware('permission:page-create', ['only' => ['store']]);
        $this->middleware('permission:page-edit', ['only' => ['update']]);
        $this->middleware('permission:page-delete', ['only' => ['destroy']]);
    }

    public function index(): ResourceCollection
    {
        return PageResource::collection($this->pageService->getAll());
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
                            $this->pageService->pageRepository->model
                        ),
                    ]
                )
            )
            ->respond(new PageResource($this->pageService->create($request->validated())));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.ok',
                    [
                        'resource' => Helper::getResourceName(
                            $this->pageService->pageRepository->model
                        ),
                    ]
                )
            )
            ->respond(new PageResource($this->pageService->findById($id)));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.updateSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->pageService->pageRepository->model
                        ),
                    ]
                )
            )
            ->respond(new PageResource($this->pageService->update($id, $request->validated())));
    }

    /**
     * @throws ReflectionException
     */
    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->pageService->delete($id);

        return $this
            ->setMessage(
                __(
                    'apiResponse.deleteSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->pageService->pageRepository->model
                        ),
                    ]
                )
            )
            ->respond(null);
    }
}
