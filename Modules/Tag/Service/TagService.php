<?php

namespace Modules\Tag\Service;

use Exception;
use Modules\Tag\Repository\TagRepository;

class TagService
{
    public TagRepository $tag_repository;
    
    public function __construct(TagRepository $tag_repository)
    {
        $this->tag_repository = $tag_repository;
    }
    
    /**
     * @param $data
     *
     * @return mixed
     */
    public function store($data): mixed
    {
        try {
            return $this->tag_repository->create($data);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return mixed|string
     */
    public function edit($id): mixed
    {
        try {
            return $this->tag_repository->findById($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return mixed|string
     */
    public function show($id): mixed
    {
        try {
            return $this->tag_repository->findById($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     * @param $data
     *
     * @return mixed|string
     */
    public function update($id, $data): mixed
    {
        try {
            return $this->tag_repository->update($id, $data);
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
            $this->tag_repository->delete($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @return mixed|string
     */
    public function getAll(): mixed
    {
        try {
            return $this->tag_repository->findAll();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}