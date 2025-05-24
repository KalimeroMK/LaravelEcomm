<?php

declare(strict_types=1);

namespace Modules\Page\Actions;

use Modules\Page\DTOs\PageDTO;
use Modules\Page\Repository\PageRepository;

readonly class CreatePageAction
{
    public function __construct(private PageRepository $repository)
    {
    }

    public function execute(array $data): PageDTO
    {
        $page = $this->repository->create($data);

        return PageDTO::fromArray($page->toArray());
    }
}
