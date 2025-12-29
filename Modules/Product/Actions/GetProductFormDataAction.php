<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Modules\Attribute\Repository\AttributeRepository;
use Modules\Brand\Repository\BrandRepository;
use Modules\Category\Repository\CategoryRepository;
use Modules\Tag\Repository\TagRepository;

readonly class GetProductFormDataAction
{
    public function __construct(
        private BrandRepository $brandRepository,
        private CategoryRepository $categoryRepository,
        private TagRepository $tagRepository,
        private AttributeRepository $attributeRepository
    ) {}

    public function execute(): array
    {
        return [
            'brands' => $this->brandRepository->findAll()->toArray(),
            'categories' => $this->categoryRepository->findAll()->toArray(),
            'tags' => $this->tagRepository->findAll()->toArray(),
            'attributes' => $this->attributeRepository->findAll(),
        ];
    }
}
