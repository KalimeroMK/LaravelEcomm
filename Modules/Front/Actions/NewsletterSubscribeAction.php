<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Http\Request;
use Modules\Newsletter\Models\Newsletter;

class NewsletterSubscribeAction
{
    public function __invoke(Request $request): bool
    {
        $email = $request->email;
        if (Newsletter::whereEmail($email)->first() === null) {
            Newsletter::create(['email' => $email]);

            return true;
        }

        return false;
    }
}
