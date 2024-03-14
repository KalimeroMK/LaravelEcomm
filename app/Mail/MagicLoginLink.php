<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\User\Models\User;

class MagicLoginLink extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        $url = route('magic.login', $this->user->magic_token);

        return $this->view('emails.magic_login_link')
            ->with(['url' => $url])
            ->subject('Your Magic Login Link');
    }
}
