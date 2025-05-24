<?php

declare(strict_types=1);

namespace Modules\Page\Actions;

use Modules\Page\DTOs\PageDTO;
use Modules\Page\Repository\PageRepository;

readonly class UpdatePageAction
{
    public function __construct(private PageRepository $repository)
    {
    }

    public function execute(int $id, array $data): PageDTO
    {
        $page = $this->repository->update($id, $data);

        return PageDTO::fromArray($page->toArray());
    }
}
