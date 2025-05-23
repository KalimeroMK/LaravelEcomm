<?php

declare(strict_types=1);

namespace Modules\Attribute\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Attribute\Actions\CreateAttributeAction;
use Modules\Attribute\Actions\DeleteAttributeAction;
use Modules\Attribute\Actions\UpdateAttributeAction;
use Modules\Attribute\DTOs\AttributeDTO;
use Modules\Attribute\Http\Requests\Api\Attribute\SearchRequest;
use Modules\Attribute\Http\Requests\Attribute\Store;
use Modules\Attribute\Http\Requests\Attribute\Update;
use Modules\Attribute\Repository\AttributeRepository;
use Modules\Attribute\Resource\AttributeResource;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\CoreController;
use ReflectionException;

class AttributeController extends CoreController
{
    public function __construct(
        public readonly AttributeRepository $repository,
        private readonly CreateAttributeAction $createAction,
        private readonly UpdateAttributeAction $updateAction,
        private readonly DeleteAttributeAction $deleteAction
    ) {
        $this->middleware('permission:bundle-list', ['only' => ['index']]);
        $this->middleware('permission:bundle-show', ['only' => ['show']]);
        $this->middleware('permission:bundle-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:bundle-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:bundle-delete', ['only' => ['destroy']]);
    }

    public function index(SearchRequest $request): AnonymousResourceCollection
    {
        return AttributeResource::collection($this->repository->search($request->validated()));
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
                            $this->repository->model
                        ),
                    ]
                )
            )
            ->respond(new AttributeResource($this->createAction->execute(AttributeDTO::fromRequest($request))));
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
                            $this->repository->model
                        ),
                    ]
                )
            )
            ->respond(new AttributeResource($this->repository->findById($id)));
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
                            $this->repository->model
                        ),
                    ]
                )
            )
            ->respond(new AttributeResource($this->updateAction->execute(AttributeDTO::fromRequest($request->validated())->withId($id))));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->deleteAction->execute($id);

        return $this
            ->setMessage(
                __(
                    'apiResponse.deleteSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->repository->model
                        ),
                    ]
                )
            )
            ->respond(null);
    }
}
