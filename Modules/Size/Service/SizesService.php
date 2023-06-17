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
     * @param $id
     *
     * @return mixed|string
     */
    public function edit($id): mixed
    {
            return $this->sizes_repository->findById($id);
    }

    /**
     * @param $id
     *
     * @return mixed|string
     */
    public function show($id): mixed
    {
            return $this->sizes_repository->findById($id);
    }

    /**
     * @param $id
     * @param $data
     *
     * @return mixed|string
     */
    public function update($id, $data): mixed
    {
            return $this->sizes_repository->update($id, $data);
    }

    /**
     * @param $id
     *
     * @return string|void
     */
    public function destroy($id)
    {
            $this->sizes_repository->delete($id);
    }

    /**
     * @return mixed|string
     */
    public function getAll(): mixed
    {
            return $this->sizes_repository->findAll();
    }

}
