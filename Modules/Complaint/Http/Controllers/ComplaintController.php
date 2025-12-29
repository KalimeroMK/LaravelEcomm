<?php

declare(strict_types=1);

namespace Modules\Complaint\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Modules\Complaint\Actions\CreateComplaintAction;
use Modules\Complaint\Actions\DeleteComplaintAction;
use Modules\Complaint\Actions\GetAllComplaintsAction;
use Modules\Complaint\Actions\UpdateComplaintAction;
use Modules\Complaint\DTOs\ComplaintDTO;
use Modules\Complaint\Http\Requests\Store;
use Modules\Complaint\Http\Requests\Update;
use Modules\Complaint\Models\Complaint;
use Modules\Core\Http\Controllers\CoreController;

class ComplaintController extends CoreController
{
    public function __construct(
        private readonly GetAllComplaintsAction $getAllComplaintsAction,
        private readonly CreateComplaintAction $createAction,
        private readonly UpdateComplaintAction $updateAction,
        private readonly DeleteComplaintAction $deleteAction
    ) {
        // Authorization is handled explicitly in each method
    }

    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $this->authorize('viewAny', Complaint::class);
        $complaints = $this->getAllComplaintsAction->execute();

        return view('complaint::index', ['complaints' => $complaints]);
    }

    public function create($order_id): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $this->authorize('create', Complaint::class);
        $complaint = new Complaint;

        return view('complaint::create', ['complaint' => $complaint, 'order_id' => $order_id]);
    }

    public function store(Store $request): RedirectResponse
    {
        $this->authorize('create', Complaint::class);
        $dto = ComplaintDTO::fromRequest($request);
        $this->createAction->execute($dto);

        return redirect()->route('admin.complaints.index')->with('success', __('messages.complaint_created_successfully'));
    }

    public function update(Update $request, Complaint $complaint): RedirectResponse
    {
        $this->authorize('update', $complaint);
        $dto = ComplaintDTO::fromRequest($request, $complaint->id);
        $this->updateAction->execute($dto);

        return redirect()->back()->with('success', __('messages.complaint_updated_successfully'));
    }

    public function destroy(Complaint $complaint): RedirectResponse
    {
        $this->authorize('delete', $complaint);
        $this->deleteAction->execute($complaint->id);

        return redirect()->route('admin.complaints.index')->with('success', __('messages.complaint_deleted_successfully'));
    }
}
