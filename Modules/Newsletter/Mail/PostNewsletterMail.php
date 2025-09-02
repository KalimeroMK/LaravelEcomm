<?php

declare(strict_types=1);

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

    public ?int $analyticsId = null;

    public string $email;

    /**
     * Create a new message instance.
     *
     * @param  array<int, Post>  $posts
     */
    public function __construct(array $posts, ?int $analyticsId = null, string $email = '')
    {
        $this->posts = $posts;
        $this->analyticsId = $analyticsId;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        return $this->view('newsletter::emails.enhanced-newsletter')
            ->with([
                'posts' => $this->posts,
                'products' => [], // Can be passed from the job
                'analyticsId' => $this->analyticsId ?? null,
                'recipientEmail' => $this->email,
            ]);
    }
}
