<?php

namespace Modules\Complaint\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Complaint\Http\Requests\Store;
use Modules\Complaint\Models\Complaint;
use Modules\Complaint\Models\ComplaintReply;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->hasRole('super-admin')) {
            $complaints = Complaint::with(['user', 'order', 'complaint_replies'])->paginate(10);
        } else {
            $complaints = Complaint::with(['user', 'order', 'complaint_replies'])->where(
                'user_id',
                auth()->id()
            )->paginate(10);
        }

        return view('complaint::index', compact('complaints'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($order_id)
    {
        $complaint = new Complaint();
        return view('complaint::create', compact('complaint', 'order_id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Store $request): RedirectResponse
    {
        Complaint::create([
            'user_id' => auth()->id(),
            'order_id' => $request->order_id,
            'complaint' => $request->complaint,
            'status' => 'open',
        ]);
        return redirect()->route('complaints.index')->with('success', 'Complaint created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Complaint $complaint)
    {
        $complaint->load(['complaint_replies.user', 'user', 'order']);
        return view('complaint::edit', compact('complaint'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'reply_content' => 'required|string',
            'status' => 'nullable|in:open,in_progress,closed',
        ]);

        // Update the complaint status
        $complaint = Complaint::findOrFail($id);
        $complaint->status = $request->status;
        $complaint->save();

        // Save the reply
        ComplaintReply::create([
            'complaint_id' => $complaint->id,
            'reply_content' => $request->reply_content,
            'user_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Complaint updated successfully.');
    }
}
