<?php

declare(strict_types=1);

namespace Modules\Newsletter\Actions;

use Modules\Newsletter\DTOs\NewsletterListDTO;
use Modules\Newsletter\Models\Newsletter;

class GetAllNewslettersAction
{
    public function execute(): NewsletterListDTO
    {
        $newsletters = Newsletter::all();

        return new NewsletterListDTO($newsletters);
    }
}
