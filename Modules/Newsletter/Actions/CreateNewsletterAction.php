<?php

declare(strict_types=1);

namespace Modules\Newsletter\Actions;

use Modules\Newsletter\DTOs\NewsletterDTO;
use Modules\Newsletter\Models\Newsletter;
use Modules\Newsletter\Repository\NewsletterRepository;

readonly class CreateNewsletterAction
{
    public function __construct(private NewsletterRepository $repository) {}

    public function execute(NewsletterDTO $dto): Newsletter
    {
        return $this->repository->create([
            'email' => $dto->email,
            'token' => $dto->token,
            'is_validated' => $dto->is_validated,
        ]);
    }
}
