<?php

declare(strict_types=1);

namespace Modules\Bundle\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Bundle\Actions\CreateBundleAction;
use Modules\Bundle\Actions\DeleteBundleAction;
use Modules\Bundle\Actions\DeleteBundleMediaAction;
use Modules\Bundle\Actions\FindBundleAction;
use Modules\Bundle\Actions\GetAllBundlesAction;
use Modules\Bundle\Actions\UpdateBundleAction;
use Modules\Bundle\DTOs\BundleDTO;
use Modules\Bundle\Http\Requests\Api\Store;
use Modules\Bundle\Http\Requests\Api\Update;
use Modules\Bundle\Http\Resource\BundleResource;
use Modules\Bundle\Models\Bundle;
use Modules\Bundle\Repository\BundleRepository;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Core\Support\Media\MediaUploader;
use Modules\Core\Support\Relations\SyncRelations;
use ReflectionException;

class BundleController extends CoreController
{
    public function __construct(
        private readonly BundleRepository $repository,
        private readonly GetAllBundlesAction $getAllBundlesAction,
        private readonly FindBundleAction $findBundleAction,
        private readonly CreateBundleAction $createAction,
        private readonly UpdateBundleAction $updateAction,
        private readonly DeleteBundleAction $deleteAction,
        private readonly DeleteBundleMediaAction $deleteBundleMediaAction
    ) {}

    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Bundle::class);

        $bundles = $this->getAllBundlesAction->execute();

        return BundleResource::collection($bundles);
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', Bundle::class);

        $dto = BundleDTO::fromRequest($request);
        $bundle = $this->createAction->execute($dto);

        SyncRelations::execute($bundle, ['products' => $dto->products]);
        MediaUploader::uploadMultiple($bundle, ['images'], 'bundle');

        return $this
            ->setMessage(__('apiResponse.storeSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new BundleResource($bundle->fresh(['media', 'products'])));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $bundle = $this->findBundleAction->execute($id);
        $this->authorize('view', $bundle);

        return $this
            ->setMessage(__('apiResponse.ok', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new BundleResource($bundle->load(['media', 'products'])));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $this->authorizeFromRepo(BundleRepository::class, 'update', $id);

        $dto = BundleDTO::fromRequest($request, $id, $this->repository->findById($id));
        $bundle = $this->updateAction->execute($dto);

        SyncRelations::execute($bundle, ['products' => $dto->products]);
        /** @var Bundle $bundle */
        MediaUploader::clearAndUpload($bundle, ['images'], 'bundle');

        return $this
            ->setMessage(__('apiResponse.updateSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new BundleResource($bundle->fresh(['media', 'products'])));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $bundle = $this->findBundleAction->execute($id);
        $this->authorize('delete', $bundle);

        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(null);
    }

    /**
     * Delete bundle media.
     */
    public function deleteMedia(int $modelId, int $mediaId): JsonResponse
    {
        $bundle = $this->findBundleAction->execute($modelId);
        $this->authorize('update', $bundle);

        $this->deleteBundleMediaAction->execute($modelId, $mediaId);

        return $this
            ->setMessage('Media deleted successfully.')
            ->respond(null);
    }
}
