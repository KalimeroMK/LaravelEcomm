<?php

declare(strict_types=1);

namespace Modules\Bundle\Service;

use Illuminate\Database\Eloquent\Model;
use Modules\Bundle\Repository\BundleRepository;
use Modules\Core\Service\CoreService;
use Modules\Product\Models\Product;

class BundleService extends CoreService
{
    public BundleRepository $bundleRepository;

    public function __construct(BundleRepository $bundleRepository)
    {
        parent::__construct($bundleRepository);
        $this->bundleRepository = $bundleRepository;
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
     * Create a new bundle with attached products.
     *
     * @param  array<string, mixed>  $data  The data for creating the bundle.
     * @return Model The newly created bundle model.
     */
    public function createWithProducts(array $data): Model
    {
        $bundle = $this->bundleRepository->create($data);
        $bundle->products()->attach($data['product']);

        // Handle image uploads
        if (array_key_exists('images', $data)) {
            $bundle->addMultipleMediaFromRequest(['images'])
                ->each(function ($fileAdder): void {
                    $fileAdder->preservingOriginal()->toMediaCollection('bundle');
                });
        }

        return $bundle;
    }

    /**
     * Update an existing bundle and sync attached products.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateWithProducts(int $id, array $data): Model
    {
        $bundle = $this->bundleRepository->findById($id);
        $bundle->update($data);
        if (isset($data['product'])) {
            $bundle->products()->sync($data['product']);
        }
        // Check for new image uploads and handle them
        if (array_key_exists('images', $data)) {
            $bundle->clearMediaCollection('bundle'); // Optionally clear existing media
            $bundle->addMultipleMediaFromRequest(['images'])
                ->each(function ($fileAdder): void {
                    $fileAdder->preservingOriginal()->toMediaCollection('bundle');
                });
        }

        return $bundle;
    }
}
