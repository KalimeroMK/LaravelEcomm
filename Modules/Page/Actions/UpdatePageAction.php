<?php

declare(strict_types=1);

namespace Modules\Page\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Page\DTOs\PageDTO;
use Modules\Page\Repository\PageRepository;

class UpdatePageAction
{
    private PageRepository $repository;

    public function __construct(PageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(PageDTO $dto): Model
    {
        $page = $this->repository->findById($dto->id);
        $page->update([
            'title' => $dto->title,
            'slug' => $dto->slug ?? $page->slug,
            'content' => $dto->content,
            'is_active' => $dto->is_active,
            'user_id' => $dto->user_id,
        ]);

        return $page;
    }
}
