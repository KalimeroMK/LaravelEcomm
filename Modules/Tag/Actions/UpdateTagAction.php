<?php

declare(strict_types=1);

namespace Modules\Tag\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Tag\DTOs\TagDTO;

readonly class UpdateTagAction
{
    public function execute(Model $tag, TagDTO $dto): Model
    {
        $tag->update([
            'title' => $dto->title,
            'slug' => $dto->slug,
            'status' => $dto->status,
        ]);

        return $tag->refresh();
    }
}
