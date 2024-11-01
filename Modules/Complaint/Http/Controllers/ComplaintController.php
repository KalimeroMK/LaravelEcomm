<?php

namespace Modules\Complaint\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Complaint\Models\Complaint;

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
    public function create()
    {
        return view('complaint::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
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
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

}
