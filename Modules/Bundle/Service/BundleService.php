<?php

namespace Modules\Bundle\Service;

use Modules\Bundle\Models\Bundle;
use Modules\Bundle\Repository\BundleRepository;
use Modules\Product\Models\Product;

class BundleService
{
    public BundleRepository $bundleRepository;

    public function __construct(BundleRepository $bundleRepository)
    {
        $this->bundleRepository = $bundleRepository;
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    public function store($data): mixed
    {
        $bundle = $this->bundleRepository->create($data);

        $bundle->products()->attach($data['product']);

        return $bundle;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function edit($id): mixed
    {
        return $this->bundleRepository->findById($id);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function show($id): mixed
    {
        return $this->bundleRepository->findById($id);
    }

    /**
     * @param $id
     * @param $data
     *
     * @return mixed|string
     */
    public function update($id, $data): mixed
    {
        return $this->bundleRepository->update((int)$id, $data);
    }

    /**
     * @param $id
     *
     * @return void
     */

    public function destroy($id): void
    {
        $this->bundleRepository->delete($id);
    }

    /**
     * @return mixed|string
     */
    public function getAll(): mixed
    {
        return $this->bundleRepository->findAll();
    }

    public function create(): array
    {
        return [
            'products' => Product::get(),
            'bundle' => new Bundle(),
        ];
    }
}