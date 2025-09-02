<?php

declare(strict_types=1);

namespace Modules\Newsletter\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Modules\Newsletter\Mail\PostNewsletterMail;

class SendNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $email;

    public array $posts;

    public ?int $analyticsId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $email, array $posts, ?int $analyticsId = null)
    {
        $this->email = $email;
        $this->posts = $posts;
        $this->analyticsId = $analyticsId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->email)->send(new PostNewsletterMail($this->posts, $this->analyticsId, $this->email));

            // Update analytics if tracking ID is provided
            if ($this->analyticsId) {
                \Modules\Newsletter\Models\EmailAnalytics::find($this->analyticsId)
                    ?->update(['sent_at' => now()]);
            }
        } catch (\Exception $e) {
            // Mark as bounced if sending fails
            if ($this->analyticsId) {
                \Modules\Newsletter\Models\EmailAnalytics::find($this->analyticsId)
                    ?->markAsBounced();
            }
            throw $e;
        }
    }
}
