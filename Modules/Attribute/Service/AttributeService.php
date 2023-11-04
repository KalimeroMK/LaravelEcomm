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

    public function getAll()
    {
        return $this->attribute_repository->findAll();
    }

    public function store(array $data)
    {
        return $this->attribute_repository->create($data);
    }

    public function show(int $id)
    {
        return $this->attribute_repository->findById($id);
    }

    public function update(int $id, array $data)
    {
        return $this->attribute_repository->update($id, $data);
    }

    public function destroy(int $id): void
    {
        $this->attribute_repository->delete($id);
    }

    public function search(array $data)
    {
        return $this->attribute_repository->search($data);
    }
}
