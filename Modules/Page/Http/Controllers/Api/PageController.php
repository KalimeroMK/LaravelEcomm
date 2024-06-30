<?php

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
    public function show($id): JsonResponse
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
    public function update(Update $request, $id): JsonResponse
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
     * @param  int  $id
     * @return JsonResponse
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
