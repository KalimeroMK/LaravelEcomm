<?php

declare(strict_types=1);

namespace Modules\Complaint\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Complaint\Actions\CreateComplaintAction;
use Modules\Complaint\Actions\DeleteComplaintAction;
use Modules\Complaint\Actions\UpdateComplaintAction;
use Modules\Complaint\DTOs\ComplaintDTO;
use Modules\Complaint\Http\Requests\Store;
use Modules\Complaint\Http\Requests\Update;
use Modules\Complaint\Models\Complaint;

class ComplaintController extends Controller
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
    }

    public function index()
    {
        $complaints = Complaint::where('user_id', auth()->id())->get();

        return view('complaint::index', ['complaints' => $complaints]);
    }

    public function create($order_id)
    {
        $complaint = new Complaint();

        return view('complaint::create', ['complaint' => $complaint, 'order_id' => $order_id]);
    }

    public function store(Store $request): RedirectResponse
    {
        $dto = ComplaintDTO::fromRequest($request);
        $this->createAction->execute($dto);

        return redirect()->route('complaints.index')->with('success', 'Complaint created successfully.');
    }

    public function update(Update $request, Complaint $complaint): RedirectResponse
    {
        $dto = ComplaintDTO::fromRequest($request, $complaint->id);
        $this->updateAction->execute($dto);

        return redirect()->back()->with('success', 'Complaint updated successfully.');
    }

    public function destroy(Complaint $complaint): RedirectResponse
    {
        $this->deleteAction->execute($complaint->id);

        return redirect()->route('complaints.index')->with('success', 'Complaint deleted successfully.');
    }
}
