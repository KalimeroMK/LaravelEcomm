<?php

declare(strict_types=1);

namespace Modules\Newsletter\Actions;

use Modules\Newsletter\DTOs\NewsletterDTO;
use Modules\Newsletter\Repository\NewsletterRepository;

readonly class CreateNewsletterAction
{
    public function __construct(private NewsletterRepository $repository) {}

    public function execute(NewsletterDTO $dto): NewsletterDTO
    {
        $newsletter = $this->repository->create([
            'email' => $dto->email,
        ]);

        return NewsletterDTO::fromArray($newsletter->toArray());
    }
}
