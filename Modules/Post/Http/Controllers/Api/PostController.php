<?php

namespace Modules\Post\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Post\Http\Requests\Api\Store;
use Modules\Post\Http\Requests\Api\Update;
use Modules\Post\Http\Resources\PostResource;
use Modules\Post\Service\PostService;

class PostController extends Controller
{
    private PostService $post_service;
    
    public function __construct(PostService $post_service)
    {
        $this->post_service = $post_service;
    }
    
    /**
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return PostResource::collection($this->post_service->getAll());
    }
    
    /**
     *
     * @return mixed
     * @throws Exception
     */
    public function store(Store $request)
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
    public function show($id)
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
    
    public function update(Update $request, $id)
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
    public function destroy($id)
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
