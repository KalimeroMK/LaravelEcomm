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
    
    public function handle()
    {
        $posts       = Post::orderBy('id', 'asc')
                           ->take(10)
                           ->get();
        $newsletters = Newsletter::whereIsValidated(true)->get();
        foreach ($newsletters as $newsletter) {
            Mail::to($newsletter->email)->send(new PostNewsletterMail($posts));
        }
    }
}
