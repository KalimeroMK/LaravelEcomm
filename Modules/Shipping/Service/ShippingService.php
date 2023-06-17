<?php

namespace Modules\Shipping\Service;

use Exception;
use Modules\Shipping\Repository\ShippingRepository;

class ShippingService
{
    public ShippingRepository $shipping_repository;

    public function __construct(ShippingRepository $shipping_repository)
    {
        $this->shipping_repository = $shipping_repository;
    }

    /**
     * @return mixed
     */
    public function getAll(): mixed
    {
        return $this->shipping_repository->findAll();
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    public function store($data): mixed
    {
        return $this->shipping_repository->create($data);
    }

    /**
     * @param $id
     * @param $data
     *
     * @return mixed
     */
    public function update($id, $data): mixed
    {
        return $this->shipping_repository->update($id, $data);
    }

    /**
     * @param $id
     *
     * @return mixed|string
     */
    public function edit($id): mixed
    {
            return $this->shipping_repository->findById($id);
    }

    /**
     * @param $id
     *
     * @return Exception|void
     */
    public function destroy($id)
    {
            $this->shipping_repository->delete($id);
    }

    /**
     * @param $id
     *
     * @return mixed|string
     */
    public function show($id): mixed
    {
            return $this->shipping_repository->findById($id);
    }

}
