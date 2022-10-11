<?php

namespace Modules\Attribute\Service;

use Exception;
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
        try {
            return $this->attribute_repository->create($data);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            return $this->attribute_repository->findById($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return mixed
     */
    public function show($id): mixed
    {
        try {
            return $this->attribute_repository->findById($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     * @param $data
     *
     * @return mixed|string
     */
    public function update($id, $data): mixed
    {
        try {
            return $this->attribute_repository->update((int)$id, $data);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return string|void
     */
    
    public function destroy($id)
    {
        try {
            $this->attribute_repository->delete($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @return mixed|string
     */
    public function getAll(): mixed
    {
        try {
            return $this->attribute_repository->findAll();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param  array  $data
     *
     * @return mixed|string
     */
    public function search(array $data): mixed
    {
        try {
            return $this->attribute_repository->search($data);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}
