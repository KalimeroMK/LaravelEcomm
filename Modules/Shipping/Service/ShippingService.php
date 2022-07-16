<?php

namespace Modules\Shipping\Service;

use Modules\Shipping\Repository\ShippingRepository;

class ShippingService
{
    private ShippingRepository $shipping_repository;
    
    public function __construct(ShippingRepository $shipping_repository)
    {
        $this->shipping_repository = $shipping_repository;
    }
    
    /**
     * @return mixed
     */
    public function index(): mixed
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
     * @return mixed
     */
    public function edit($id): mixed
    {
        return $this->shipping_repository->findById($id);
    }
    
    /**
     * @param $id
     *
     * @return void
     */
    public function destroy($id): void
    {
        $this->shipping_repository->delete($id);
    }
    
}