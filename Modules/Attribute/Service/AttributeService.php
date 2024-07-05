<?php

namespace Modules\Attribute\Service;

use Modules\Attribute\Repository\AttributeRepository;
use Modules\Core\Service\CoreService;

class AttributeService extends CoreService
{
    public AttributeRepository $attribute_repository;

    public function __construct(AttributeRepository $attributeRepository)
    {
        parent::__construct($attributeRepository);
        $this->attribute_repository = $attributeRepository;
    }

    /**
     * Search for attributes based on given criteria.
     *
     * @param  array<string, mixed>  $data  The search criteria.
     */
    public function search(array $data): mixed
    {
        return $this->attribute_repository->search($data);
    }
}
