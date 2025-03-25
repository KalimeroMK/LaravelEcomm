<?php

declare(strict_types=1);

namespace Modules\Attribute\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Attribute\Http\Requests\Api\SearchRequest;
use Modules\Attribute\Http\Requests\Store;
use Modules\Attribute\Http\Requests\Update;
use Modules\Attribute\Resource\AttributeResource;
use Modules\Attribute\Service\AttributeService;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\CoreController;
use ReflectionException;

class AttributeController extends CoreController
{
    public AttributeService $attribute_service;

    public function __construct(AttributeService $attribute_service)
    {
        $this->attribute_service = $attribute_service;
        $this->middleware('permission:attribute-list', ['only' => ['index']]);
        $this->middleware('permission:attribute-show', ['only' => ['show']]);
        $this->middleware('permission:attribute-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:attribute-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:attribute-delete', ['only' => ['destroy']]);
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
