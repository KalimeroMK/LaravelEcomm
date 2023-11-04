<?php

namespace Modules\Newsletter\Models\Observer;

use Illuminate\Support\Str;
use Modules\Newsletter\Models\Newsletter;

class NewsletterObserver
{
    public function creating(Newsletter $newsletter): void
    {
        $newsletter->token = Str::random(64);
    }
}