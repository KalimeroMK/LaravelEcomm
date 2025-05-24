<?php

declare(strict_types=1);

namespace Modules\Brand\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Brand\DTOs\BrandDTO;
use Modules\Brand\Repository\BrandRepository;
use Modules\Brand\Actions\CreateBrandAction;
use Modules\Brand\Actions\UpdateBrandAction;
use Modules\Brand\Actions\DeleteBrandAction;
use Modules\Brand\Http\Requests\Api\Store;
use Modules\Brand\Http\Requests\Api\Update;
use Modules\Brand\Http\Resource\BrandResource;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use ReflectionException;

class BrandController extends CoreController
{
    public function __construct(
        public readonly BrandRepository $repository,
        private readonly CreateBrandAction $createAction,
        private readonly UpdateBrandAction $updateAction,
        private readonly DeleteBrandAction $deleteAction
    ) {
        $this->middleware('permission:brand-list', ['only' => ['index']]);
        $this->middleware('permission:brand-show', ['only' => ['show']]);
        $this->middleware('permission:brand-create', ['only' => ['store']]);
        $this->middleware('permission:brand-update', ['only' => ['update']]);
        $this->middleware('permission:brand-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request): ResourceCollection
    {
        return BrandResource::collection($this->repository->search($request->all()));
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        $dto = BrandDTO::fromRequest($request);
        $brand = $this->createAction->execute($dto);
        return $this
            ->setMessage(
                __('apiResponse.storeSuccess', [
                    'resource' => Helper::getResourceName($this->repository->model),
                ])
            )
            ->respond(new BrandResource($brand));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $brand = $this->repository->findById($id);
        return $this
            ->setMessage(
                __('apiResponse.ok', [
                    'resource' => Helper::getResourceName($this->repository->model),
                ])
            )
            ->respond(new BrandResource($brand));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $dto = BrandDTO::fromArray($request->validated() + ['id' => $id]);
        $brand = $this->updateAction->execute($dto);
        return $this
            ->setMessage(
                __('apiResponse.updateSuccess', [
                    'resource' => Helper::getResourceName($this->repository->model),
                ])
            )
            ->respond(new BrandResource($brand));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->deleteAction->execute($id);
        return $this
            ->setMessage(
                __('apiResponse.deleteSuccess', [
                    'resource' => Helper::getResourceName($this->repository->model),
                ])
            )
            ->respond(null);
    }
}
