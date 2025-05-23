<?php

declare(strict_types=1);

namespace Modules\Newsletter\DTOs;

use Modules\Newsletter\Models\Newsletter;

class NewsletterDTO
{
    public int $id;

    public string $email;

    public string $created_at;

    public function __construct(Newsletter $newsletter)
    {
        $this->id = $newsletter->id;
        $this->email = $newsletter->email;
        $this->created_at = $newsletter->created_at->toDateTimeString();
    }
}
