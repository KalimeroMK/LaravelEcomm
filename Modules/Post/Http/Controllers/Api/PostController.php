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
use Modules\Post\Models\Post;
use Modules\Post\Service\PostService;
use ReflectionException;

class PostController extends CoreController
{

    private PostService $post_service;

    public function __construct(PostService $post_service)
    {
        $this->post_service = $post_service;
        $this->authorizeResource(Post::class, 'post');
    }

    /**
     * @param Search $request
     *
     * @return ResourceCollection
     */
    public function index(Search $request): ResourceCollection
    {
        return PostResource::collection($this->post_service->search($request->validated()));
    }

    /**
     *
     * @param Store $request
     * @return JsonResponse
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
                            $this->post_service->post_repository->model
                        ),
                    ]
                )
            )
            ->respond(new PostResource($this->post_service->store($request->validated())));
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
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
     * @param Update $request
     * @param $id
     * @return JsonResponse|string
     * @throws ReflectionException
     */
    public function update(Update $request, $id): JsonResponse|string
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
     * @param $id
     * @return JsonResponse|string
     * @throws ReflectionException
     */
    public function destroy($id): JsonResponse|string
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
