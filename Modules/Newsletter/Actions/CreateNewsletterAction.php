<?php

declare(strict_types=1);

namespace Modules\Newsletter\Actions;

use Modules\Newsletter\DTOs\NewsletterDTO;
use Modules\Newsletter\Models\Newsletter;

class CreateNewsletterAction
{
    public function execute(array $data): NewsletterDTO
    {
        $newsletter = Newsletter::create($data);

        return new NewsletterDTO($newsletter);
    }
}
