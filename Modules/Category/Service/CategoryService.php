<?php

namespace Modules\Category\Service;

use Modules\Category\Repository\CategoryRepository;

class CategoryService
{
    public CategoryRepository $category_repository;

    public function __construct(CategoryRepository $category_repository)
    {
        $this->category_repository = $category_repository;
    }

    public function getAll()
    {
        return $this->category_repository->findAll();
    }

    public function store(array $data)
    {
        return $this->category_repository->create($data);
    }

    public function show(int $id)
    {
        return $this->category_repository->findById($id);
    }

    public function edit(int $id)
    {
        return $this->category_repository->findById($id);
    }

    public function update(int $id, array $data)
    {
        return $this->category_repository->update($id, $data);
    }

    public function destroy(int $id): void
    {
        $this->category_repository->delete($id);
    }
}
