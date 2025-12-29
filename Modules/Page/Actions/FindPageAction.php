<?php

declare(strict_types=1);

namespace Modules\Page\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Page\Repository\PageRepository;

readonly class FindPageAction
{
    public function __construct(private PageRepository $repository) {}

    public function execute(int $id): Model
    {
        return $this->repository->findById($id);
    }
}
