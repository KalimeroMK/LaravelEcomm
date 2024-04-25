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
     * @return mixed|string
     */
    public function getAll(): mixed
    {
        return $this->bundleRepository->findAll();
    }

    /**
     * Prepares data needed to create a new bundle.
     *
     * @return array<string, mixed> Data needed for creating a new bundle.
     */
    public function create(): array
    {
        return [
            'products' => Product::get(),
            'bundle' => new Bundle(),
        ];
    }

    /**
     * Store a new bundle.
     *
     * @param  array<string, mixed>  $data  The data to create the attribute.
     * @return mixed
     */
    public function store(array $data): mixed
    {
        $bundle = $this->bundleRepository->create($data);

        $bundle->products()->attach($data['product']);

        return $bundle;
    }

    /**
     * Retrieves data necessary for editing a bundle.
     *
     * @param  int  $id  The ID of the bundle to edit.
     * @return array<string, mixed> Data needed for the edit operation.
     */
    public function edit(int $id): array
    {
        return [
            'products' => Product::all(),
            'bundle' => $this->bundleRepository->findById($id),
        ];
    }

    /**
     * Update an existing attribute.
     *
     * @param  int  $id  The bundle ID to update.
     * @param  array<string, mixed>  $data  The data for updating the attribute.
     * @return mixed
     */
    public function update(int $id, array $data): mixed
    {
        return $this->bundleRepository->update($id, $data);
    }

    /**
     * @param  int  $id
     *
     * @return mixed
     */
    public function show(int $id): mixed
    {
        return $this->bundleRepository->findById($id);
    }

    /**
     * @param  int  $id
     *
     * @return void
     */

    public function destroy(int $id): void
    {
        $this->bundleRepository->delete($id);
    }


}