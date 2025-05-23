<?php

declare(strict_types=1);

namespace Modules\Newsletter\Actions;

use Modules\Newsletter\DTOs\NewsletterDTO;
use Modules\Newsletter\Repository\NewsletterRepository;

readonly class UpdateNewsletterAction
{
    public function __construct(private NewsletterRepository $repository)
    {
    }

    public function execute(int $id, array $data): NewsletterDTO
    {
        $newsletter = $this->repository->update($id, $data);

        return NewsletterDTO::fromArray($newsletter->toArray());
    }
}
