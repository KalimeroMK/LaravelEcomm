<?php

declare(strict_types=1);

namespace Modules\Page\Actions;

use Modules\Page\DTOs\PageDTO;
use Modules\Page\Models\Page;
use Modules\Page\Repository\PageRepository;

readonly class CreatePageAction
{
    private PageRepository $repository;

    public function __construct(PageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(PageDTO $dto): Page
    {
        return $this->repository->create([
            'title' => $dto->title,
            'slug' => $dto->slug,
            'content' => $dto->content,
            'is_active' => $dto->is_active,
            'user_id' => $dto->user_id,
        ]);
    }
}
