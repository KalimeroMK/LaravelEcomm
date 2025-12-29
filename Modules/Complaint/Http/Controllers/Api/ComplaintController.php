<?php

declare(strict_types=1);

namespace Modules\Complaint\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Complaint\Actions\CreateComplaintAction;
use Modules\Complaint\Actions\DeleteComplaintAction;
use Modules\Complaint\Actions\FindComplaintAction;
use Modules\Complaint\Actions\GetAllComplaintsAction;
use Modules\Complaint\Actions\UpdateComplaintAction;
use Modules\Complaint\DTOs\ComplaintDTO;
use Modules\Complaint\Http\Requests\Store;
use Modules\Complaint\Http\Requests\Update;
use Modules\Complaint\Http\Resources\ComplaintResource;
use Modules\Complaint\Models\Complaint;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use ReflectionException;

class ComplaintController extends CoreController
{
    public function __construct(
        private readonly GetAllComplaintsAction $getAllComplaintsAction,
        private readonly FindComplaintAction $findComplaintAction,
        private readonly CreateComplaintAction $createAction,
        private readonly UpdateComplaintAction $updateAction,
        private readonly DeleteComplaintAction $deleteAction
    ) {
        // permission middleware removed, policies are used instead
    }

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Complaint::class);

        $complaints = $this->getAllComplaintsAction->execute();

        return ComplaintResource::collection($complaints);
    }

    public function create(int $orderId): JsonResponse
    {
        $this->authorize('create', Complaint::class);

        return $this
            ->setMessage(__('apiResponse.ok'))
            ->respond(['order_id' => $orderId]);
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', Complaint::class);

        $dto = ComplaintDTO::fromRequest($request);
        $complaint = $this->createAction->execute($dto);

        return $this
            ->setMessage(__('apiResponse.storeSuccess', [
                'resource' => Helper::getResourceName(Complaint::class),
            ]))
            ->respond(new ComplaintResource($complaint));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $complaint = $this->findComplaintAction->execute($id);
        $this->authorize('view', $complaint);

        return $this
            ->setMessage(__('apiResponse.ok', [
                'resource' => Helper::getResourceName(Complaint::class),
            ]))
            ->respond(new ComplaintResource($complaint->load(['user', 'order'])));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $complaint = $this->findComplaintAction->execute($id);
        $this->authorize('update', $complaint);

        $dto = ComplaintDTO::fromRequest($request, $id);
        $complaint = $this->updateAction->execute($dto);

        return $this
            ->setMessage(__('apiResponse.updateSuccess', [
                'resource' => Helper::getResourceName(Complaint::class),
            ]))
            ->respond(new ComplaintResource($complaint->load(['user', 'order'])));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $complaint = $this->findComplaintAction->execute($id);
        $this->authorize('delete', $complaint);

        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', [
                'resource' => Helper::getResourceName(Complaint::class),
            ]))
            ->respond(null);
    }
}
