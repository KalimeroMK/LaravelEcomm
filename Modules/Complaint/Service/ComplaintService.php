<?php

namespace Modules\Complaint\Service;

use Modules\Complaint\Jobs\SendComplaintEmailJob;
use Modules\Complaint\Models\Complaint;
use Modules\Complaint\Models\ComplaintReply;
use Modules\Complaint\Repository\ComplaintRepository;
use Modules\Core\Service\CoreService;

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
            'description' => $data['description'],
            'status' => 'open',
        ]);
        foreach (['admin', 'user'] as $recipient) {
            SendComplaintEmailJob::dispatch($complaint, $recipient)->onQueue('emails');
        }

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
        foreach (['admin', 'user'] as $recipient) {
            SendComplaintEmailJob::dispatch($complaint, $recipient)->onQueue('emails');
        }

        ComplaintReply::create([
            'complaint_id' => $complaint->id,
            'reply_content' => $data['reply_content'],
            'user_id' => $data['user_id'],
        ]);

        return $complaint;
    }
}
