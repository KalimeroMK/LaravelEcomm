<?php

namespace Modules\Tag\Service;

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
            return $this->tag_repository->create($data);
    }

    /**
     * @param $id
     *
     * @return mixed|string
     */
    public function edit($id): mixed
    {
            return $this->tag_repository->findById($id);
    }

    /**
     * @param $id
     *
     * @return mixed|string
     */
    public function show($id): mixed
    {
            return $this->tag_repository->findById($id);
    }

    /**
     * @param $id
     * @param $data
     *
     * @return mixed|string
     */
    public function update($id, $data): mixed
    {
            return $this->tag_repository->update($id, $data);
    }

    /**
     * @param $id
     *
     * @return string|void
     */
    public function destroy($id)
    {
            $this->tag_repository->delete($id);
    }

    /**
     * @return mixed|string
     */
    public function getAll(): mixed
    {
            return $this->tag_repository->findAll();
    }
}
