<?php

declare(strict_types=1);

namespace Modules\Attribute\Actions;

use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Repository\AttributeRepository;

readonly class FindAttributeAction
{
    public function __construct(private AttributeRepository $repository) {}

    public function execute(int $id): Attribute
    {
        /** @var Attribute */
        return $this->repository->findById($id);
    }
}
