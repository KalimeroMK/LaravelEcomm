<?php

namespace Modules\Tag\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Size\Http\Requests\Api\Store;
use Modules\Size\Http\Requests\Api\Store as Update;
use Modules\Tag\Http\Resources\TagResource;
use Modules\Tag\Models\Tag;
use Modules\Tag\Service\TagService;

class TagController extends CoreController
{

    private TagService $tag_service;

    public function __construct(TagService $tag_service)
    {
        $this->tag_service = $tag_service;
        $this->authorizeResource(Tag::class, 'tag');
    }

    /**
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return TagResource::collection($this->tag_service->getAll());
    }

    /**
     *
     * @return mixed
     * @throws Exception
     */
    public function store(Store $request)
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
            ->respond(new TagResource($this->tag_service->store($request->validated())));
    }

    /**
     * @param $id
     *
     * @return JsonResponse|string
     */
    public function show($id)
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
            ->respond(new TagResource($this->tag_service->show($id)));
    }

    public function update(Update $request, $id)
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
     * @param $id
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
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
            ->respond($this->tag_service->destroy($id));
    }
}
