<?php

namespace Modules\Shipping\Service;

use Modules\Shipping\Repository\ShippingRepository;

class ShippingService
{
    public ShippingRepository $shipping_repository;

    public function __construct(ShippingRepository $shipping_repository)
    {
        $this->shipping_repository = $shipping_repository;
    }

    /**
     * Get all shipping records.
     *
     * @return object
     */
    public function getAll(): object
    {
        return $this->shipping_repository->findAll();
    }

    /**
     * Store a new shipping record.
     *
     * @param  array<string, mixed>  $data
     * @return mixed
     */
    public function store(array $data): mixed
    {
        return $this->shipping_repository->create($data);
    }

    /**
     * Update an existing shipping record.
     *
     * @param  int  $id
     * @param  array<string, mixed>  $data
     * @return mixed
     */
    public function update(int $id, array $data): mixed
    {
        return $this->shipping_repository->update($id, $data);
    }

    /**
     * Get the data for editing a shipping record.
     *
     * @param  int  $id
     * @return mixed
     */
    public function edit(int $id): mixed
    {
        return $this->shipping_repository->findById($id);
    }

    /**
     * Delete a shipping record.
     *
     * @param  int  $id
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->shipping_repository->delete($id);
    }

    /**
     * Show a specific shipping record.
     *
     * @param  int  $id
     * @return mixed
     */
    public function show(int $id): mixed
    {
        return $this->shipping_repository->findById($id);
    }
}
