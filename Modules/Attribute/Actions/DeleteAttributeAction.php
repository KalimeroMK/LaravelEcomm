<?php

declare(strict_types=1);

namespace Modules\Attribute\Actions;

use Modules\Attribute\Repository\AttributeRepository;

readonly class DeleteAttributeAction
{
    public function __construct(private AttributeRepository $repository) {}

    public function execute(int $id): bool
    {
        $attribute = $this->repository->findById($id);

        return $attribute && $attribute->delete();
    }
}
