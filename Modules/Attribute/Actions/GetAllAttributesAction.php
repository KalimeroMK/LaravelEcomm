<?php

declare(strict_types=1);

namespace Modules\Attribute\Actions;

use Illuminate\Support\Collection;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Repository\AttributeRepository;

readonly class GetAllAttributesAction
{
    public function __construct(private AttributeRepository $repository) {}

    /**
     * @return Collection<int, Attribute>
     */
    public function execute(): Collection
    {
        return $this->repository->findAll();
    }
}
