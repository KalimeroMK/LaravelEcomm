<?php

namespace Modules\Complaint\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Complaint\Models\Complaint;
use Modules\Complaint\Models\ComplaintReply;

class ComplaintReplyController extends Controller
{
    public function store(Request $request, Complaint $complaint)
    {
        $request->validate([
            'reply_content' => 'required|string',
        ]);

        ComplaintReply::create([
            'complaint_id' => $complaint->id,
            'user_id' => auth()->id(),
            'reply_content' => $request->reply_content,
        ]);

        return redirect()->back()->with('success', 'Reply added successfully.');
    }
}
