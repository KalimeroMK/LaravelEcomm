<?php

declare(strict_types=1);

namespace Modules\Newsletter\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Newsletter\DTOs\NewsletterDTO;
use Modules\Newsletter\Repository\NewsletterRepository;

readonly class UpdateNewsletterAction
{
    public function __construct(private NewsletterRepository $repository) {}

    public function execute(NewsletterDTO $dto): Model
    {
        return $this->repository->update($dto->id, [
            'email' => $dto->email,
            'token' => $dto->token,
            'is_validated' => $dto->is_validated,
        ]);
    }
}
