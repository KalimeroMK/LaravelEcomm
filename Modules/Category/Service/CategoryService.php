<?php

namespace Modules\Category\Service;

use Modules\Category\Repository\CategoryRepository;

class CategoryService
{
    public CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAll()
    {
        return $this->categoryRepository->findAll();
    }

    public function store(array $data)
    {
        return $this->categoryRepository->create($data);
    }

    public function show(int $id)
    {
        return $this->categoryRepository->findById($id);
    }

    public function edit(int $id)
    {
        return $this->categoryRepository->findById($id);
    }

    public function update(int $id, array $data)
    {
        return $this->categoryRepository->update($id, $data);
    }

    public function destroy(int $id): void
    {
        $this->categoryRepository->delete($id);
    }
}
