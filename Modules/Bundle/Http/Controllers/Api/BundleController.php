<?php

declare(strict_types=1);

namespace Modules\Bundle\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Bundle\Actions\CreateBundleAction;
use Modules\Bundle\Actions\DeleteBundleAction;
use Modules\Bundle\Actions\UpdateBundleAction;
use Modules\Bundle\DTO\BundleDTO;
use Modules\Bundle\Http\Requests\Store;
use Modules\Bundle\Http\Requests\Update;
use Modules\Bundle\Http\Resource\BundleResource;
use Modules\Bundle\Models\Bundle;
use Modules\Core\Http\Controllers\Api\CoreController;
use ReflectionException;
use Throwable;

class BundleController extends CoreController
{
    private CreateBundleAction $createAction;
    private UpdateBundleAction $updateAction;
    private DeleteBundleAction $deleteAction;

    public function __construct(
        CreateBundleAction $createAction,
        UpdateBundleAction $updateAction,
        DeleteBundleAction $deleteAction
    ) {
        $this->createAction = $createAction;
        $this->updateAction = $updateAction;
        $this->deleteAction = $deleteAction;
        $this->middleware('permission:bundle-list', ['only' => ['index']]);
        $this->middleware('permission:bundle-show', ['only' => ['show']]);
        $this->middleware('permission:bundle-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:bundle-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:bundle-delete', ['only' => ['destroy']]);
    }

    public function index(): ResourceCollection
    {
        return BundleResource::collection(Bundle::all());
    }

    /**
     * @param  Store  $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(Store $request): JsonResponse
    {
        $dto = BundleDTO::fromRequest($request);
        $bundle = $this->createAction->execute($dto);
        return $this
            ->setMessage(__('apiResponse.storeSuccess', [
                'resource' => 'Bundle',
            ]))
            ->respond(new BundleResource($bundle));
    }

    /**
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $bundle = Bundle::findOrFail($id);
        return $this
            ->setMessage(__('apiResponse.ok', [
                'resource' => 'Bundle',
            ]))
            ->respond(new BundleResource($bundle));
    }

    /**
     * @throws ReflectionException|Throwable
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $dto = BundleDTO::fromRequest($request)->withId($id);
        $bundle = $this->updateAction->execute($dto);
        return $this
            ->setMessage(__('apiResponse.updateSuccess', [
                'resource' => 'Bundle',
            ]))
            ->respond(new BundleResource($bundle));
    }

    /**
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->deleteAction->execute($id);
        return $this
            ->setMessage(__('apiResponse.deleteSuccess', [
                'resource' => 'Bundle',
            ]))
            ->respond(null);
    }
}
