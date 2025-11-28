<?php

declare(strict_types=1);

namespace Modules\Newsletter\Actions;

use Modules\Newsletter\Repository\NewsletterRepository;

readonly class DeleteNewsletterAction
{
    public function __construct(private NewsletterRepository $repository) {}

    public function execute(int $id): void
    {
        $this->repository->destroy($id);
    }
}
