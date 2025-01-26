<?php

namespace Modules\Size\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Size\Http\Requests\Api\Store;
use Modules\Size\Http\Requests\Api\Update;
use Modules\Size\Http\Resources\SizeResource;
use Modules\Size\Service\SizesService;
use ReflectionException;

class SizeController extends CoreController
{
    private SizesService $sizes_service;

    public function __construct(SizesService $sizes_service)
    {
        $this->sizes_service = $sizes_service;
        $this->middleware('permission:size-list', ['only' => ['index']]);
        $this->middleware('permission:size-show', ['only' => ['show']]);
        $this->middleware('permission:size-create', ['only' => ['store']]);
        $this->middleware('permission:size-edit', ['only' => ['update']]);
        $this->middleware('permission:size-delete', ['only' => ['destroy']]);
    }

    public function index(): ResourceCollection
    {
        return SizeResource::collection($this->sizes_service->getAll());
    }

    /**
     * @throws Exception
     */
    public function store(Store $request): \Illuminate\Http\JsonResponse|string
    {
        try {
            return $this
                ->setMessage(
                    __(
                        'apiResponse.storeSuccess',
                        [
                            'resource' => Helper::getResourceName(
                                $this->sizes_service->sizes_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new SizeResource($this->sizes_service->store($request->validated())));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function show(int $id): \Illuminate\Http\JsonResponse|string
    {
        try {
            return $this
                ->setMessage(
                    __(
                        'apiResponse.ok',
                        [
                            'resource' => Helper::getResourceName(
                                $this->sizes_service->sizes_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new SizeResource($this->sizes_service->show($id)));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function update(Update $request, int $id): JsonResponse|string
    {
        try {
            return $this
                ->setMessage(
                    __(
                        'apiResponse.updateSuccess',
                        [
                            'resource' => Helper::getResourceName(
                                $this->sizes_service->sizes_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new SizeResource($this->sizes_service->update($id, $request->validated())));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->sizes_service->delete($id);

        return $this
            ->setMessage(
                __(
                    'apiResponse.deleteSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->sizes_service->sizes_repository->model
                        ),
                    ]
                )
            )
            ->respond(null);
    }
}
