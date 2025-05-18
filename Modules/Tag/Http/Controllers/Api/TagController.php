<?php

declare(strict_types=1);

namespace Modules\Tag\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Tag\Http\Requests\Api\Store;
use Modules\Tag\Http\Requests\Api\Update;
use Modules\Tag\Http\Resources\TagResource;
use Modules\Tag\Service\TagService;
use ReflectionException;

class TagController extends CoreController
{
    private TagService $tag_service;

    public function __construct(TagService $tag_service)
    {
        $this->tag_service = $tag_service;
        $this->middleware('permission:tag-list', ['only' => ['index']]);
        $this->middleware('permission:tag-show', ['only' => ['show']]);
        $this->middleware('permission:tag-create', ['only' => ['store']]);
        $this->middleware('permission:tag-edit', ['only' => ['update']]);
        $this->middleware('permission:tag-delete', ['only' => ['destroy']]);
    }

    public function index(): ResourceCollection
    {
        return TagResource::collection($this->tag_service->getAll());
    }

    /**
     * @throws Exception
     */
    public function store(Store $request): JsonResponse
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.storeSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->tag_service->tag_repository->model
                        ),
                    ]
                )
            )
            ->respond(new TagResource($this->tag_service->create($request->validated())));
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
                            $this->tag_service->tag_repository->model
                        ),
                    ]
                )
            )
            ->respond(new TagResource($this->tag_service->findById($id)));
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
                            $this->tag_service->tag_repository->model
                        ),
                    ]
                )
            )
            ->respond(new TagResource($this->tag_service->update($id, $request->validated())));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->tag_service->delete($id);

        return $this
            ->setMessage(
                __(
                    'apiResponse.deleteSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->tag_service->tag_repository->model
                        ),
                    ]
                )
            )
            ->respond(null);
    }
}
