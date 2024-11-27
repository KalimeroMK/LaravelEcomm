<?php

namespace Modules\Complaint\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Complaint\Http\Requests\Store;
use Modules\Complaint\Http\Requests\Update;
use Modules\Complaint\Http\Resources\ComplaintResource;
use Modules\Complaint\Service\ComplaintService;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\CoreController;
use ReflectionException;

class ComplaintController extends CoreController
{
    public ComplaintService $complaint_service;

    public function __construct(ComplaintService $complaint_service)
    {
        $this->complaint_service = $complaint_service;

        $this->middleware('permission:complaint-list', ['only' => ['index']]);
        $this->middleware('permission:complaint-show', ['only' => ['show']]);
        $this->middleware('permission:complaint-create', ['only' => ['store']]);
        $this->middleware('permission:complaint-update', ['only' => ['update']]);
        $this->middleware('permission:complaint-delete', ['only' => ['destroy']]);
    }

    public function index(): AnonymousResourceCollection
    {
        return ComplaintResource::collection($this->complaint_service->getComplaintsForUser(Auth()->user()));
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
                            $this->complaint_service->complaintRepository->model
                        ),
                    ]
                )
            )
            ->respond(new ComplaintResource($this->complaint_service->create($request->validated())));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $complaint = $this->complaint_service->findById($id);

        return $this
            ->setMessage(
                __('apiResponse.ok', [
                    'resource' => Helper::getResourceName($this->complaint_service->complaintRepository->model),
                ])
            )
            ->respond(new ComplaintResource($complaint));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $complaint = $this->complaint_service->updateComplaint($id, $request->validated());

        return $this
            ->setMessage(
                __('apiResponse.updateSuccess', [
                    'resource' => Helper::getResourceName($this->complaint_service->complaintRepository->model),
                ])
            )
            ->respond(new ComplaintResource($complaint));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->complaint_service->delete($id);

        return $this
            ->setMessage(
                __('apiResponse.deleteSuccess', [
                    'resource' => Helper::getResourceName($this->complaint_service->complaintRepository->model),
                ])
            )
            ->respond(null);
    }
}
