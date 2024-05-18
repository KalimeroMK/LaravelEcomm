<?php

namespace Modules\Size\Service;

use Modules\Core\Service\CoreService;
use Modules\Size\Repository\SizesRepository;

class SizesService extends CoreService
{

    public SizesRepository $sizes_repository;

    public function __construct(SizesRepository $sizes_repository)
    {
        $this->sizes_repository = $sizes_repository;
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    public function store($data): mixed
    {
        return $this->sizes_repository->create($data);
    }

    /**
     * @param  int  $id
     *
     * @return mixed|string
     */
    public function edit(int $id): mixed
    {
        return $this->sizes_repository->findById($id);
    }

    /**
     * @param  int  $id
     *
     * @return mixed|string
     */
    public function show(int $id): mixed
    {
        return $this->sizes_repository->findById($id);
    }

    /**
     * @param  int  $id
     * @param $data
     *
     * @return mixed|string
     */
    public function update(int $id, $data):
    {
        return $this->sizes_repository->update($id, $data);
    }

    /**
     * @param  int  $id
     *
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->sizes_repository->delete($id);
    }

    /**
     * @return object
     */
    public function getAll(): object
    {
        return $this->sizes_repository->findAll();
    }

}
