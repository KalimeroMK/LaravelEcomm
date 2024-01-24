<?php

namespace Modules\Attribute\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Modules\Attribute\Http\Requests\Api\SearchRequest;
use Modules\Attribute\Resource\AttributeResource;
use Modules\Attribute\Service\AttributeService;
use Modules\Brand\Http\Requests\Api\Update;
use Modules\Core\Helpers\Helper;
use Modules\Core\Traits\ApiResponses;
use Modules\Coupon\Http\Requests\Api\Store;
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
            ->respond(new AttributeResource($this->attribute_service->store($request->validated())));
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
                            $this->attribute_service->attribute_repository->model
                        ),
                    ]
                )
            )
            ->respond(new AttributeResource($this->attribute_service->show($id)));
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
                            $this->attribute_service->attribute_repository->model
                        ),
                    ]
                )
            )
            ->respond(new AttributeResource($this->attribute_service->update($id, $request->all())));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy($id): JsonResponse
    {
        $this->attribute_service->destroy($id);
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
