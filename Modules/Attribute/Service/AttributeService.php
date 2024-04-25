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
     * Get all attributes.
     *
     * @return object
     */
    public function getAll(): object
    {
        return $this->attribute_repository->findAll();
    }

    /**
     * Store a new attribute.
     *
     * @param  array<string, mixed>  $data  The data to create the attribute.
     * @return mixed
     */
    public function store(array $data): mixed
    {
        return $this->attribute_repository->create($data);
    }

    /**
     * Show details of an attribute.
     *
     * @param  int  $id  The attribute ID.
     * @return mixed
     */
    public function show(int $id): mixed
    {
        return $this->attribute_repository->findById($id);
    }

    /**
     * Update the specified coupon.
     * @param  int  $id  The ID of the coupon to update.
     * @param  array<string, mixed>  $data  Data to update the coupon.
     * @return bool Result of the update operation.
     */
    public function update(int $id, array $data,): mixed
    {
        return $this->attribute_repository->update($id, $data);
    }

    /**
     * Delete an attribute.
     *
     * @param  int  $id  The attribute ID.
     */
    public function destroy(int $id): void
    {
        $this->attribute_repository->delete($id);
    }

    /**
     * Search for attributes based on given criteria.
     *
     * @param  array<string, mixed>  $data  The search criteria.
     * @return mixed
     */
    public function search(array $data): mixed
    {
        return $this->attribute_repository->search($data);
    }
}
