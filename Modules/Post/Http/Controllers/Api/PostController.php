<?php

namespace Modules\Post\Http\Controllers\Api;

use Exception;
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

class PostController extends CoreController
{
    
    private PostService $post_service;
    
    public function __construct(PostService $post_service)
    {
        $this->post_service = $post_service;
        $this->authorizeResource(Post::class);
    }
    
    /**
     * @param  Search  $request
     *
     * @return ResourceCollection
     */
    public function index(Search $request): ResourceCollection
    {
        return PostResource::collection($this->post_service->getAll($request->validated()));
    }
    
    /**
     *
     * @param  Store  $request
     *
     * @return mixed
     */
    public function store(Store $request): mixed
    {
        try {
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
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse|string
     */
    public function show($id): JsonResponse|string
    {
        try {
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
                ->respond(new PostResource($this->post_service->show($id)));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param  Update  $request
     * @param $id
     *
     * @return JsonResponse|string
     */
    public function update(Update $request, $id): JsonResponse|string
    {
        try {
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
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse|string
     */
    public function destroy($id): JsonResponse|string
    {
        try {
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
                ->respond($this->post_service->destroy($id));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}
