<?php

namespace Modules\Newsletter\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PostNewsletterMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private array $posts;

    public function __construct($posts)
    {
        $this->posts = $posts;
    }

    public function build(): PostNewsletterMail
    {
        return $this->markdown('newsletter::emails.post-newsletter')->with('posts', $this->posts);
    }
}
