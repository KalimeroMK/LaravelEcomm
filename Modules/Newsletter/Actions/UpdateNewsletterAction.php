<?php

declare(strict_types=1);

namespace Modules\Newsletter\Actions;

use Modules\Newsletter\DTOs\NewsletterDTO;
use Modules\Newsletter\Models\Newsletter;

class UpdateNewsletterAction
{
    public function execute(int $id, array $data): NewsletterDTO
    {
        $newsletter = Newsletter::findOrFail($id);
        $newsletter->update($data);

        return new NewsletterDTO($newsletter);
    }
}
