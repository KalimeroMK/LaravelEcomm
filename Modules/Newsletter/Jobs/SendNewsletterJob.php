<?php

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

    /**
     * Create a new job instance.
     */
    public function __construct(string $email, array $posts)
    {
        $this->email = $email;
        $this->posts = $posts;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->email)->send(new PostNewsletterMail($this->posts));
    }
}
