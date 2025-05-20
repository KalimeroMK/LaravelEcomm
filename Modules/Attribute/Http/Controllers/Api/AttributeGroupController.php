<?php

declare(strict_types=1);

namespace Modules\Attribute\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Attribute\Actions\AttributeGroup\CreateAttributeGroupAction;
use Modules\Attribute\Actions\AttributeGroup\DeleteAttributeGroupAction;
use Modules\Attribute\Actions\AttributeGroup\UpdateAttributeGroupAction;
use Modules\Attribute\DTO\AttributeGroupDTO;
use Modules\Attribute\Http\Requests\Api\AttributeGroup\Store;
use Modules\Attribute\Http\Requests\Api\AttributeGroup\Update;
use Modules\Attribute\Http\Resources\AttributeGroupResource;
use Modules\Attribute\Repository\AttributeGroupRepository;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use ReflectionException;

class AttributeGroupController extends CoreController
{
    public function __construct(
        private readonly AttributeGroupRepository $repository,
        private readonly CreateAttributeGroupAction $createAction,
        private readonly UpdateAttributeGroupAction $updateAction,
        private readonly DeleteAttributeGroupAction $deleteAction
    ) {
        $this->middleware('permission:attribute-group-list', ['only' => ['index']]);
        $this->middleware('permission:attribute-group-show', ['only' => ['show']]);
        $this->middleware('permission:attribute-group-create', ['only' => ['store']]);
        $this->middleware('permission:attribute-group-update', ['only' => ['update']]);
        $this->middleware('permission:attribute-group-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return AttributeGroupResource::collection(
            $this->repository->findAll()
        );
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        return $this
            ->setMessage(__('apiResponse.storeSuccess', [
                'resource' => Helper::getResourceName($this->repository->model),
            ]))
            ->respond(new AttributeGroupResource(
                $this->createAction->execute(AttributeGroupDTO::fromRequest($request))
            ));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        return $this
            ->setMessage(__('apiResponse.ok', [
                'resource' => Helper::getResourceName($this->repository->model),
            ]))
            ->respond(new AttributeGroupResource($this->repository->findById($id)));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        return $this
            ->setMessage(__('apiResponse.updateSuccess', [
                'resource' => Helper::getResourceName($this->repository->model),
            ]))
            ->respond(new AttributeGroupResource(
                $this->updateAction->execute(
                    AttributeGroupDTO::fromRequest($request)->withId($id)
                )
            ));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', [
                'resource' => Helper::getResourceName($this->repository->model),
            ]))
            ->respond(null);
    }
}
