<?php

declare(strict_types=1);

namespace Modules\Complaint\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Complaint\Actions\CreateComplaintAction;
use Modules\Complaint\Actions\DeleteComplaintAction;
use Modules\Complaint\Actions\UpdateComplaintAction;
use Modules\Complaint\DTOs\ComplaintDTO;
use Modules\Complaint\Http\Requests\Store;
use Modules\Complaint\Http\Requests\Update;
use Modules\Complaint\Http\Resources\ComplaintResource;
use Modules\Complaint\Models\Complaint;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\CoreController;

class ComplaintController extends CoreController
{
    private CreateComplaintAction $createAction;

    private UpdateComplaintAction $updateAction;

    private DeleteComplaintAction $deleteAction;

    public function __construct(
        CreateComplaintAction $createAction,
        UpdateComplaintAction $updateAction,
        DeleteComplaintAction $deleteAction
    ) {
        $this->createAction = $createAction;
        $this->updateAction = $updateAction;
        $this->deleteAction = $deleteAction;

        $this->middleware('permission:complaint-list', ['only' => ['index']]);
        $this->middleware('permission:complaint-show', ['only' => ['show']]);
        $this->middleware('permission:complaint-create', ['only' => ['store']]);
        $this->middleware('permission:complaint-update', ['only' => ['update']]);
        $this->middleware('permission:complaint-delete', ['only' => ['destroy']]);
    }

    public function index(): AnonymousResourceCollection
    {
        return ComplaintResource::collection(Complaint::where('user_id', auth()->id())->get());
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        $dto = ComplaintDTO::fromRequest($request);
        $complaint = $this->createAction->execute($dto);

        return $this
            ->setMessage(
                __(
                    'apiResponse.storeSuccess',
                    [
                        'resource' => Helper::getResourceName(Complaint::class),
                    ]
                )
            )
            ->respond(new ComplaintResource($complaint));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $complaint = Complaint::findOrFail($id);

        return $this
            ->setMessage(
                __('apiResponse.ok', [
                    'resource' => Helper::getResourceName(Complaint::class),
                ])
            )
            ->respond(new ComplaintResource($complaint));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $dto = ComplaintDTO::fromRequest($request, $id);
        $complaint = $this->updateAction->execute($dto);

        return $this
            ->setMessage(
                __('apiResponse.updateSuccess', [
                    'resource' => Helper::getResourceName(Complaint::class),
                ])
            )
            ->respond(new ComplaintResource($complaint));
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
                    'resource' => Helper::getResourceName(Complaint::class),
                ])
            )
            ->respond(null);
    }
}
