<?php

declare(strict_types=1);

namespace Modules\Newsletter\DTOs;

class NewsletterListDTO
{
    public array $newsletters;

    public function __construct($newsletters)
    {
        $this->newsletters = $newsletters->toArray();
    }
}
