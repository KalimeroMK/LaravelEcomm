<?php

declare(strict_types=1);

namespace Modules\Complaint\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Modules\Complaint\Mail\ComplaintCreated;
use Modules\Complaint\Models\Complaint;

class SendComplaintEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Complaint $complaint;

    public string $recipientType;

    /**
     * Maximum attempts for the job.
     */
    public int $tries = 3;

    /**
     * Time before retrying a failed job (in seconds).
     */
    public int $backoff = 30;

    public function __construct(Complaint $complaint, string $recipientType)
    {
        $this->complaint = $complaint;
        $this->recipientType = $recipientType;
    }

    public function handle(): void
    {
        Mail::to($this->getRecipientEmail())->send(
            new ComplaintCreated($this->complaint, $this->recipientType)
        );
    }

    private function getRecipientEmail(): string
    {
        return $this->recipientType === 'admin'
            ? config('admin.admin_email')
            : $this->complaint->user->email;
    }
}
