<?php

namespace Modules\Attribute\Service;

use Modules\Attribute\Repository\AttributeRepository;
use Modules\Core\Service\CoreService;

class AttributeService extends CoreService
{

    public AttributeRepository $attribute_repository;

    public function __construct(AttributeRepository $attribute_repository)
    {
        $this->attribute_repository = $attribute_repository;
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    public function store($data): mixed
    {
      return $this->attribute_repository->create($data);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function edit($id): mixed
    {
        return $this->attribute_repository->findById($id);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function show($id): mixed
    {
        return $this->attribute_repository->findById($id);
    }

    /**
     * @param $id
     * @param $data
     *
     * @return mixed|string
     */
    public function update($id, $data): mixed
    {

            return $this->attribute_repository->update((int)$id, $data);
    }

    /**
     * @param $id
     *
     * @return string|void
     */

    public function destroy($id)
    {
        $this->attribute_repository->delete($id);
    }

    /**
     * @return mixed|string
     */
    public function getAll(): mixed
    {

            return $this->attribute_repository->findAll();
    }

    /**
     * @param  array  $data
     *
     * @return mixed|string
     */
    public function search(array $data): mixed
    {
            return $this->attribute_repository->search($data);
    }
}
