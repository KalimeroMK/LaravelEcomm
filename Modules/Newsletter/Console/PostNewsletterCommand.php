<?php

namespace Modules\Newsletter\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Modules\Newsletter\Mail\PostNewsletterMail;
use Modules\Newsletter\Models\Newsletter;
use Modules\Post\Models\Post;

class PostNewsletterCommand extends Command
{
    protected $signature = 'newsletter:post';

    protected $description = 'Command description';

    public function handle(): void
    {
        $posts = Post::orderBy('id')
            ->take(10)
            ->get()
            ->all(); // Convert the collection to an array

        $newsletters = Newsletter::whereIsValidated(true)->get();

        foreach ($newsletters as $newsletter) {
            Mail::to($newsletter->email)->send(new PostNewsletterMail($posts));
        }
    }
}
