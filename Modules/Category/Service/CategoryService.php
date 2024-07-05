<?php

namespace Modules\Category\Service;

use Modules\Category\Repository\CategoryRepository;
use Modules\Core\Service\CoreService;

class CategoryService extends CoreService
{
    public CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        parent::__construct($categoryRepository);
        $this->categoryRepository = $categoryRepository;
    }
}
