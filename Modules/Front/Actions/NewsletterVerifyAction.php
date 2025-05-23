<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Modules\Newsletter\Models\Newsletter;

class NewsletterVerifyAction
{
    public function __invoke(string $token): bool
    {
        $newsletter = Newsletter::where('token', $token)->first();
        if ($newsletter !== null) {
            $newsletter->is_verified = true;
            $newsletter->save();

            return true;
        }

        return false;
    }
}
