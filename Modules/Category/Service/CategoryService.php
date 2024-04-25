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

    /**
     *
     * @return mixed
     */
    public function getAll(): mixed
    {
        return $this->categoryRepository->findAll();
    }

    /**
     * Store a new attribute.
     *
     * @param  array<string, mixed>  $data  The data to create the attribute.
     * @return mixed
     */
    public function store(array $data): mixed
    {
        return $this->categoryRepository->create($data);
    }

    /**
     * @param  int  $id
     *
     * @return mixed
     */
    public function show(int $id): mixed
    {
        return $this->categoryRepository->findById($id);
    }

    /**
     * @param  int  $id
     *
     * @return mixed
     */
    public function edit(int $id): mixed
    {
        return $this->categoryRepository->findById($id);
    }

    /**
     * Update an existing attribute.
     *
     * @param  int  $id  The attribute ID to update.
     * @param  array<string, mixed>  $data  The data for updating the attribute.
     * @return mixed
     */
    public function update(int $id, array $data): mixed
    {
        return $this->categoryRepository->update($id, $data);
    }

    public function destroy(int $id): void
    {
        $this->categoryRepository->delete($id);
    }
}
