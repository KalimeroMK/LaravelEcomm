<?php

declare(strict_types=1);

namespace Modules\Bundle\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Bundle\Actions\CreateBundleAction;
use Modules\Bundle\Actions\DeleteBundleAction;
use Modules\Bundle\Actions\UpdateBundleAction;
use Modules\Bundle\DTO\BundleDTO;
use Modules\Bundle\Http\Requests\Store;
use Modules\Bundle\Http\Requests\Update;
use Modules\Bundle\Http\Resource\BundleResource;
use Modules\Bundle\Models\Bundle;
use Modules\Bundle\Repository\BundleRepository;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\CoreController;
use ReflectionException;
use Throwable;

class BundleController extends CoreController
{
    public function __construct(
        public BundleRepository $repository,
        private readonly CreateBundleAction $createAction,
        private readonly UpdateBundleAction $updateAction,
        private readonly DeleteBundleAction $deleteAction
    ) {
        $this->middleware('permission:bundle-list', ['only' => ['index']]);
        $this->middleware('permission:bundle-show', ['only' => ['show']]);
        $this->middleware('permission:bundle-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:bundle-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:bundle-delete', ['only' => ['destroy']]);
    }

    public function index(): AnonymousResourceCollection
    {
        return BundleResource::collection($this->repository->findAll());
    }

    /**
     * @throws Throwable
     */
    public function store(Store $request): JsonResponse
    {
        $bundle = $this->createAction->execute(BundleDTO::fromRequest($request));

        return $this
            ->setMessage(__('apiResponse.storeSuccess', [
                'resource' => Helper::getResourceName(Bundle::class),
            ]))
            ->respond(new BundleResource($bundle));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $bundle = Bundle::findOrFail($id);

        return $this
            ->setMessage(__('apiResponse.ok', [
                'resource' => Helper::getResourceName(Bundle::class),
            ]))
            ->respond(new BundleResource($bundle));
    }

    /**
     * @throws Throwable|ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $bundle = $this->updateAction->execute(BundleDTO::fromRequest($request)->withId($id));

        return $this
            ->setMessage(__('apiResponse.updateSuccess', [
                'resource' => Helper::getResourceName(Bundle::class),
            ]))
            ->respond(new BundleResource($bundle));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', [
                'resource' => Helper::getResourceName(Bundle::class),
            ]))
            ->respond(null);
    }
}
