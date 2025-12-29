<?php

declare(strict_types=1);

namespace Modules\Newsletter\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Newsletter\Repository\NewsletterRepository;

readonly class FindNewsletterAction
{
    public function __construct(private NewsletterRepository $repository) {}

    public function execute(int $id): Model
    {
        return $this->repository->findById($id);
    }
}
