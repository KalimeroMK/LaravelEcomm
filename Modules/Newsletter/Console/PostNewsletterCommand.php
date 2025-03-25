<?php

declare(strict_types=1);

namespace Modules\Newsletter\Console;

use Illuminate\Console\Command;
use Modules\Newsletter\Jobs\SendNewsletterJob;
use Modules\Newsletter\Models\Newsletter;
use Modules\Post\Models\Post;

class PostNewsletterCommand extends Command
{
    protected $signature = 'newsletter:post';

    protected $description = 'Send newsletters with the latest posts';

    public function handle(): void
    {
        // Get the latest 10 posts
        $posts = Post::orderBy('id')
            ->take(10)
            ->get()
            ->all(); // Convert the collection to an array

        // Get validated newsletters
        $newsletters = Newsletter::whereIsValidated(true)->get();

        // Dispatch the newsletter jobs
        foreach ($newsletters as $newsletter) {
            SendNewsletterJob::dispatch($newsletter->email, $posts);
        }

        $this->info('Newsletter jobs dispatched successfully.');
    }
}
