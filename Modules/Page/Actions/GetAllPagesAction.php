<?php

declare(strict_types=1);

namespace Modules\Page\Actions;

use Modules\Page\DTOs\PageListDTO;
use Modules\Page\Repository\PageRepository;

readonly class GetAllPagesAction
{
    public function __construct(public PageRepository $repository)
    {
    }

    public function execute(): PageListDTO
    {
        $pages = $this->repository->findAll();

        return new PageListDTO($pages);
    }
}
