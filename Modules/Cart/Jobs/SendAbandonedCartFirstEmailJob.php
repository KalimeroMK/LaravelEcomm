<?php

declare(strict_types=1);

namespace Modules\Cart\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Modules\Cart\Mail\AbandonedCartFirstEmail;
use Modules\Cart\Models\AbandonedCart;

class SendAbandonedCartFirstEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public AbandonedCart $abandonedCart;

    /**
     * Maximum attempts for the job.
     */
    public int $tries = 3;

    /**
     * Time before retrying a failed job (in seconds).
     */
    public int $backoff = 30;

    public function __construct(AbandonedCart $abandonedCart)
    {
        $this->abandonedCart = $abandonedCart;
    }

    public function handle(): void
    {
        // Check if email should still be sent
        if (!$this->abandonedCart->shouldSendFirstEmail()) {
            return;
        }

        $email = $this->abandonedCart->email ?? $this->abandonedCart->user?->email;

        if (!$email) {
            return;
        }

        Mail::to($email)->send(new AbandonedCartFirstEmail($this->abandonedCart));

        // Mark first email as sent
        $this->abandonedCart->update(['first_email_sent' => now()]);
    }
}
