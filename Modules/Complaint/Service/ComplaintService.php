<?php

namespace Modules\Complaint\Service;

use Illuminate\Support\Facades\Mail;
use Modules\Complaint\Mail\ComplaintCreated;
use Modules\Complaint\Models\Complaint;
use Modules\Complaint\Models\ComplaintReply;
use Modules\Complaint\Repository\ComplaintRepository;
use Modules\Core\Service\CoreService;
use Modules\User\Models\User;

class ComplaintService extends CoreService
{
    public ComplaintRepository $complaintRepository;

    public function __construct(ComplaintRepository $complaintRepository)
    {
        parent::__construct($complaintRepository);
        $this->complaintRepository = $complaintRepository;
    }

    public function getComplaintsForUser($user)
    {
        if ($user->hasRole('super-admin')) {
            return Complaint::with(['user', 'order', 'complaint_replies'])->paginate(10);
        }

        return Complaint::with(['user', 'order', 'complaint_replies'])
            ->where('user_id', $user->id)
            ->paginate(10);
    }

    /**
     * Create a new complaint.
     */
    public function createComplaint(int $userId, array $data): Complaint
    {
        $complaint = Complaint::create([
            'user_id' => $userId,
            'order_id' => $data['order_id'],
            'complaint' => $data['complaint'],
            'status' => 'open',
        ]);
        $this->sendEmails($complaint);
        return $complaint;
    }

    /**
     * Update complaint status and save a reply.
     */
    public function updateComplaint(int $id, array $data)
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->status = $data['status'] ?? $complaint->status;
        $complaint->save();
        $this->sendEmails($complaint);

        ComplaintReply::create([
            'complaint_id' => $complaint->id,
            'reply_content' => $data['reply_content'],
            'user_id' => $data['user_id'],
        ]);

        return $complaint;
    }

    /**
     * Send emails to the user and relevant admins.
     */
    public function sendEmails(Complaint $complaint): void
    {
        // Notify the user
        Mail::to($complaint->user->email)->send(new ComplaintCreated($complaint, 'user'));

        // Notify super-admins
        $relevantAdmins = User::role('super-admin')->get();
        foreach ($relevantAdmins as $admin) {
            Mail::to($admin->email)->send(new ComplaintCreated($complaint, 'admin'));
        }
    }
}
