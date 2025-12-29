<?php

declare(strict_types=1);

namespace Modules\Product\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Product\Actions\DeleteProductAction;
use Modules\Product\Actions\DeleteProductMediaAction;
use Modules\Product\Actions\GenerateProductDescriptionAction;
use Modules\Product\Actions\GetAllProductsAction;
use Modules\Product\Actions\GetFeaturedProductsAction;
use Modules\Product\Actions\GetProductFormDataAction;
use Modules\Product\Actions\StoreProductAction;
use Modules\Product\Actions\UpdateProductAction;
use Modules\Product\DTOs\ProductDTO;
use Modules\Product\Http\Requests\Store;
use Modules\Product\Http\Requests\Update;
use Modules\Product\Models\Product;
use Modules\Product\Repository\ProductRepository;

class ProductController extends CoreController
{
    public function __construct(
        private readonly GetAllProductsAction $getAllProductsAction,
        private readonly GetFeaturedProductsAction $getFeaturedProductsAction,
        private readonly GetProductFormDataAction $getProductFormDataAction,
        private readonly StoreProductAction $storeProductAction,
        private readonly UpdateProductAction $updateProductAction,
        private readonly DeleteProductAction $deleteProductAction,
        private readonly DeleteProductMediaAction $deleteProductMediaAction,
        private readonly GenerateProductDescriptionAction $generateDescriptionAction,
        private readonly ProductRepository $productRepository
    ) {
        $this->authorizeResource(Product::class, 'product');
    }

    public function index(): Renderable
    {
        $productsDto = $this->getAllProductsAction->execute();
        $hotProducts = $this->getFeaturedProductsAction->execute();

        return view('product::index', [
            'products' => $productsDto->products,
            'hot_products' => $hotProducts,
        ]);
    }

    public function create(): Renderable
    {
        $formData = $this->getProductFormDataAction->execute();

        return view('product::create', array_merge($formData, ['product' => []]));
    }

    public function store(Store $request): RedirectResponse
    {
        $dto = ProductDTO::fromRequest($request);
        $this->storeProductAction->execute($dto);

        return redirect()->route('admin.products.index');
    }

    public function edit(Product $product): Renderable
    {
        $formData = $this->getProductFormDataAction->execute();
        $product->load(['attributeValues.attribute']);

        return view('product::edit', array_merge($formData, ['product' => $product]));
    }

    public function update(Update $request, Product $product): RedirectResponse
    {
        $dto = ProductDTO::fromRequest($request, $product->id);
        $this->updateProductAction->execute($product->id, $dto);

        return redirect()->route('admin.products.index');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->deleteProductAction->execute($product->id);

        return redirect()->route('admin.products.index');
    }

    public function deleteMedia(int $modelId, int $mediaId): RedirectResponse
    {
        $this->deleteProductMediaAction->execute($modelId, $mediaId);

        return back()->with('success', 'Media deleted successfully.');
    }

    public function generateDescription(Request $request): JsonResponse
    {
        $title = $request->title;
        $description = $this->generateDescriptionAction->execute($title);

        return response()->json(['description' => $description]);
    }
}
