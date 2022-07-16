<?php

namespace Modules\Category\Service;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use LaravelIdea\Helper\Modules\Category\Models\_IH_Category_C;
use Modules\Category\Models\Category;
use Modules\Category\Repository\CategoryRepository;

class CategoryService
{
    
    private CategoryRepository $category_repository;
    
    public function __construct(CategoryRepository $category_repository)
    {
        $this->category_repository = $category_repository;
    }
    
    /**
     * @return mixed
     */
    public function index(): mixed
    {
        try {
            return $this->category_repository->findAll();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $data
     *
     * @return Collection|_IH_Category_C|mixed|Category|Category[]
     */
    public function store($data): mixed
    {
        try {
            return $this->category_repository->create($data);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return mixed|string
     */
    public function edit($id)
    {
        try {
            return $this->category_repository->findById($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $data
     *
     * @return mixed
     */
    public function update($data): mixed
    {
        try {
            return $this->category_repository->update($data['id'], $data);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return string|void
     */
    public function destroy($id)
    {
        try {
            $this->category_repository->delete($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
}