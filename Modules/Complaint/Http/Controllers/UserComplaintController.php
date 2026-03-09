<?php

declare(strict_types=1);

namespace Modules\Complaint\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Modules\Complaint\Actions\CreateComplaintAction;
use Modules\Complaint\DTOs\ComplaintDTO;
use Modules\Complaint\Http\Requests\Store;
use Modules\Complaint\Models\Complaint;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Order\Models\Order;

class UserComplaintController extends CoreController
{
    public function __construct(
        private readonly CreateComplaintAction $createAction
    ) {
        $this->middleware('auth');
    }

    /**
     * Display user's complaints
     */
    public function index(): View|Factory|Application
    {
        $complaints = Complaint::where('user_id', Auth::id())
            ->with('order')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('complaint::user.index', ['complaints' => $complaints]);
    }

    /**
     * Show a specific complaint (only if owned by user)
     */
    public function show(Complaint $complaint): View|Factory|Application
    {
        // Ensure user can only view their own complaints
        if ($complaint->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this complaint.');
        }

        $complaint->load(['order', 'replies.user']);

        return view('complaint::user.show', ['complaint' => $complaint]);
    }

    /**
     * Show form to create a complaint for an order
     */
    public function create(Order $order): View|Factory|Application
    {
        // Ensure user can only create complaints for their own orders
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        // Check if complaint already exists for this order
        $existingComplaint = Complaint::where('order_id', $order->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingComplaint) {
            return redirect()->route('user.complaints.show', $existingComplaint)
                ->with('info', __('messages.complaint_already_exists'));
        }

        return view('complaint::user.create', ['order' => $order]);
    }

    /**
     * Store a new complaint
     */
    public function store(Store $request, Order $order): RedirectResponse
    {
        // Ensure user can only create complaints for their own orders
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $dto = ComplaintDTO::fromRequest($request);
        $this->createAction->execute($dto);

        return redirect()->route('user.complaints.index')
            ->with('success', __('messages.complaint_created_successfully'));
    }
}
