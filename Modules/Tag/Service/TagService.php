<?php

namespace Modules\Tag\Service;

use Modules\Tag\Repository\TagRepository;

class TagService
{
    private TagRepository $tag_repository;
    
    public function __construct(TagRepository $tag_repository)
    {
        $this->tag_repository = $tag_repository;
    }
    
    /**
     * @return mixed
     */
    public function index(): mixed
    {
        return $this->tag_repository->findAll();
    }
    
    /**
     * @param $data
     *
     * @return mixed
     */
    public function store($data): mixed
    {
        return $this->tag_repository->create($data);
    }
    
    /**
     * @param $id
     * @param $data
     *
     * @return mixed
     */
    public function update($id, $data): mixed
    {
        return $this->tag_repository->update($id, $data);
    }
    
    /**
     * @param $id
     *
     * @return mixed
     */
    public function edit($id): mixed
    {
        return $this->tag_repository->findById($id);
    }
    
    /**
     * @param $id
     *
     * @return void
     */
    public function destroy($id): void
    {
        $this->tag_repository->delete($id);
    }
}