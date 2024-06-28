<?php

namespace Modules\Size\Service;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Modules\Core\Service\CoreService;
use Modules\Size\Repository\SizesRepository;

class SizesService extends CoreService
{

    public SizesRepository $sizes_repository;

    public function __construct(SizesRepository $sizes_repository)
    {
        parent::__construct($sizes_repository);
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
     * @return Model|null
     */
    public function edit(int $id): ?Model
    {
        return $this->sizes_repository->findById($id);
    }

    /**
     * @param  int  $id
     *
     * @return Model
     */
    public function show(int $id): Model
    {
        return $this->sizes_repository->findById($id);
    }

    /**
     * @param  int  $id
     * @param  array  $data
     *
     * @return Model
     */
    public function update(int $id, array $data): Model
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
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->sizes_repository->findAll();
    }

}
