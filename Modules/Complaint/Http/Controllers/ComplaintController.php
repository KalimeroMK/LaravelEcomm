<?php

namespace Modules\Complaint\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Complaint\Http\Requests\Store;
use Modules\Complaint\Http\Requests\Update;
use Modules\Complaint\Models\Complaint;
use Modules\Complaint\Service\ComplaintService;

class ComplaintController extends Controller
{
    protected ComplaintService $complaintService;

    public function __construct(ComplaintService $complaintService)
    {
        $this->complaintService = $complaintService;
    }

    public function index()
    {
        $complaints = $this->complaintService->getComplaintsForUser(auth()->user());
        return view('complaint::index', compact('complaints'));
    }

    public function create($order_id)
    {
        $complaint = new Complaint();
        return view('complaint::create', compact('complaint', 'order_id'));
    }

    public function store(Store $request): RedirectResponse
    {
        $this->complaintService->createComplaint(auth()->id(), $request->all());

        return redirect()->route('complaints.index')->with('success', 'Complaint created successfully.');
    }

    public function update(Update $request, $id)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $this->complaintService->updateComplaint($id, $data);

        return redirect()->back()->with('success', 'Complaint updated successfully.');
    }
}
