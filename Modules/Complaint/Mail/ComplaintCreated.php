<?php

namespace Modules\Complaint\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Complaint\Models\Complaint;

class ComplaintCreated extends Mailable
{
    use Queueable, SerializesModels;

    public Complaint $complaint;
    public string $recipientType;

    public function __construct(Complaint $complaint, string $recipientType)
    {
        $this->complaint = $complaint;
        $this->recipientType = $recipientType;
    }

    public function build(): ComplaintCreated
    {
        return $this
            ->subject("New Complaint Created: {$this->complaint->id}")
            ->view('complaint::email.complaint_created')
            ->with([
                'complaint' => $this->complaint,
                'recipientType' => $this->recipientType,
            ]);
    }
}