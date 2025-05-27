<?php

declare(strict_types=1);

namespace Modules\Attribute\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Attribute\Actions\AttributeGroup\CreateAttributeGroupAction;
use Modules\Attribute\Actions\AttributeGroup\DeleteAttributeGroupAction;
use Modules\Attribute\Actions\AttributeGroup\UpdateAttributeGroupAction;
use Modules\Attribute\DTOs\AttributeGroupDTO;
use Modules\Attribute\Http\Requests\Api\AttributeGroup\Store;
use Modules\Attribute\Http\Requests\Api\AttributeGroup\Update;
use Modules\Attribute\Http\Resources\AttributeGroupResource;
use Modules\Attribute\Models\AttributeGroup;
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
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', AttributeGroup::class);

        return AttributeGroupResource::collection(
            $this->repository->findAll()
        );
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', AttributeGroup::class);

        return $this
            ->setMessage(__('apiResponse.storeSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
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
        $attributeGroup = $this->authorizeFromRepo(AttributeGroupRepository::class, 'view', $id);

        return $this
            ->setMessage(__('apiResponse.ok', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new AttributeGroupResource($attributeGroup));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $this->authorizeFromRepo(AttributeGroupRepository::class, 'update', $id);

        return $this
            ->setMessage(__('apiResponse.updateSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new AttributeGroupResource(
                $this->updateAction->execute(AttributeGroupDTO::fromRequest($request)->withId($id))
            ));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->authorizeFromRepo(AttributeGroupRepository::class, 'delete', $id);

        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(null);
    }
}
