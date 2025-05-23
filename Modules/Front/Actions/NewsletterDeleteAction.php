<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Modules\Newsletter\Models\Newsletter;

class NewsletterDeleteAction
{
    public function __invoke(string $token): bool
    {
        $newsletter = Newsletter::where('token', $token)->first();
        if ($newsletter !== null) {
            $newsletter->delete();

            return true;
        }

        return false;
    }
}
