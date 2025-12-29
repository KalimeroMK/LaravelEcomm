<?php

declare(strict_types=1);

namespace Modules\Newsletter\Actions;

use Illuminate\Support\Collection;
use Modules\Newsletter\Repository\NewsletterRepository;

readonly class GetAllNewslettersAction
{
    public function __construct(private NewsletterRepository $repository) {}

    public function execute(): Collection
    {
        return $this->repository->findAll();
    }
}
