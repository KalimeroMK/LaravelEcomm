<?php

namespace Modules\Attribute\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Modules\Attribute\Http\Requests\Api\SearchRequest;
use Modules\Attribute\Http\Requests\Store;
use Modules\Attribute\Http\Requests\Update;
use Modules\Attribute\Resource\AttributeResource;
use Modules\Attribute\Service\AttributeService;
use Modules\Core\Helpers\Helper;
use Modules\Core\Traits\ApiResponses;
use ReflectionException;

class AttributeController extends Controller
{
    use ApiResponses;

    public AttributeService $attribute_service;

    public function __construct(AttributeService $attribute_service)
    {
        $this->attribute_service = $attribute_service;
    }

    public function index(SearchRequest $request): AnonymousResourceCollection
    {
        return AttributeResource::collection($this->attribute_service->search($request->validated()));
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
                            $this->attribute_service->attribute_repository->model
                        ),
                    ]
                )
            )
            ->respond(new AttributeResource($this->attribute_service->create($request->validated())));
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
                            $this->attribute_service->attribute_repository->model
                        ),
                    ]
                )
            )
            ->respond(new AttributeResource($this->attribute_service->findById($id)));
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
                            $this->attribute_service->attribute_repository->model
                        ),
                    ]
                )
            )
            ->respond(new AttributeResource($this->attribute_service->update($id, $request->validated())));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->attribute_service->delete($id);
        return $this
            ->setMessage(
                __(
                    'apiResponse.deleteSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->attribute_service->attribute_repository->model
                        ),
                    ]
                )
            )
            ->respond(null);
    }
}
