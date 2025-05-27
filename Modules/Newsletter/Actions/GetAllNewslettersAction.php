<?php

declare(strict_types=1);

namespace Modules\Newsletter\Actions;

use Modules\Newsletter\DTOs\NewsletterListDTO;
use Modules\Newsletter\Repository\NewsletterRepository;

readonly class GetAllNewslettersAction
{
    public function __construct(private NewsletterRepository $repository) {}

    public function execute(): NewsletterListDTO
    {
        $newsletters = $this->repository->findAll();

        return new NewsletterListDTO($newsletters);
    }
}
