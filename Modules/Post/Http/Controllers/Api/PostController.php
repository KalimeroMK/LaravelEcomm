<?php

namespace Modules\Post\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Post\Http\Requests\Api\Search;
use Modules\Post\Http\Requests\Api\Store;
use Modules\Post\Http\Requests\Api\Update;
use Modules\Post\Http\Resources\PostResource;
use Modules\Post\Service\PostService;
use ReflectionException;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class PostController extends CoreController
{
    private PostService $post_service;

    public function __construct(PostService $post_service)
    {
        $this->post_service = $post_service;
        $this->middleware('permission:post-list', ['only' => ['index']]);
        $this->middleware('permission:post-show', ['only' => ['show']]);
        $this->middleware('permission:post-create', ['only' => ['store']]);
        $this->middleware('permission:post-edit', ['only' => ['update']]);
        $this->middleware('permission:post-delete', ['only' => ['destroy']]);
    }

    public function index(Search $request): ResourceCollection
    {
        return PostResource::collection($this->post_service->search($request->validated()));
    }

    /**
     * @throws ReflectionException
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function store(Store $request): JsonResponse
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.storeSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->post_service->post_repository->model
                        ),
                    ]
                )
            )
            ->respond(new PostResource($this->post_service->create($request->validated())));
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
                            $this->post_service->post_repository->model
                        ),
                    ]
                )
            )
            ->respond(new PostResource($this->post_service->findById($id)));
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse|string
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.updateSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->post_service->post_repository->model
                        ),
                    ]
                )
            )
            ->respond(new PostResource($this->post_service->update($id, $request->validated())));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse|string
    {
        $this->post_service->delete($id);

        return $this
            ->setMessage(
                __(
                    'apiResponse.deleteSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->post_service->post_repository->model
                        ),
                    ]
                )
            )
            ->respond(null);
    }
}
