<?php

namespace Modules\Newsletter\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Post\Models\Post;

/**
 * Class PostNewsletterMail
 *
 * @property array<int, Post> $posts
 */
class PostNewsletterMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The posts for the newsletter.
     *
     * @var array<int, Post>
     */
    private array $posts;

    /**
     * Create a new message instance.
     *
     * @param  array<int, Post>  $posts
     */
    public function __construct(array $posts)
    {
        $this->posts = $posts;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        return $this->markdown('newsletter::emails.post-newsletter')
            ->with('posts', $this->posts);
    }
}
