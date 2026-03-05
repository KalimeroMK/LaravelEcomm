<?php

declare(strict_types=1);

namespace Modules\Product\Http\Controllers;

use Exception;
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
use Modules\Product\Services\ConfigurableProductService;

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
        private readonly ProductRepository $productRepository,
        private readonly ConfigurableProductService $configurableProductService,
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

        return view('product::create', array_merge($formData, [
            'product' => [],
            'configurableAttributes' => [],
        ]));
    }

    public function store(Store $request): RedirectResponse
    {
        $dto = ProductDTO::fromRequest($request);
        $product = $this->storeProductAction->execute($dto);

        // Handle configurable product creation
        if ($request->input('type') === Product::TYPE_CONFIGURABLE) {
            $this->handleConfigurableProduct($product, $request);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): Renderable
    {
        $formData = $this->getProductFormDataAction->execute();
        $product->load(['attributeValues.attribute', 'variants.attributeValues.attribute']);

        // Get configurable attributes if this is a configurable product
        $configurableAttributes = [];
        if ($product->isConfigurable()) {
            $configurableAttributes = $product->getConfigurableAttributes();
        }

        return view('product::edit', array_merge($formData, [
            'product' => $product,
            'configurableAttributes' => $configurableAttributes,
            'variants' => $product->variants,
        ]));
    }

    public function update(Update $request, Product $product): RedirectResponse
    {
        $dto = ProductDTO::fromRequest($request, $product->id);
        $this->updateProductAction->execute($product->id, $dto);

        // Handle configurable product updates
        if ($product->isConfigurable()) {
            $this->handleConfigurableProductUpdate($product, $request);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Delete variants first if configurable
        if ($product->isConfigurable()) {
            $this->configurableProductService->deleteVariants($product);
        }

        $this->deleteProductAction->execute($product->id);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
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

    /**
     * Generate variants for configurable product
     */
    public function generateVariants(Request $request, Product $product): JsonResponse
    {
        if (! $product->isConfigurable()) {
            return response()->json([
                'success' => false,
                'message' => 'Product is not configurable',
            ], 422);
        }

        try {
            $attributeCodes = $request->input('attributes', []);
            $variants = $this->configurableProductService->generateVariants($product, $attributeCodes);

            return response()->json([
                'success' => true,
                'message' => count($variants).' variants generated successfully',
                'variants' => $variants->map(fn ($v) => [
                    'id' => $v->id,
                    'name' => $v->variant_name,
                    'sku' => $v->sku,
                    'price' => $v->price,
                    'stock' => $v->stock,
                ]),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update variant prices and stock
     */
    public function updateVariants(Request $request, Product $product): JsonResponse
    {
        if (! $product->isConfigurable()) {
            return response()->json([
                'success' => false,
                'message' => 'Product is not configurable',
            ], 422);
        }

        $prices = $request->input('prices', []);
        $stocks = $request->input('stocks', []);

        $this->configurableProductService->updateVariantPrices($product, $prices);
        $this->configurableProductService->updateVariantStock($product, $stocks);

        return response()->json([
            'success' => true,
            'message' => 'Variants updated successfully',
        ]);
    }

    /**
     * Delete all variants
     */
    public function deleteVariants(Request $request, Product $product): JsonResponse
    {
        if (! $product->isConfigurable()) {
            return response()->json([
                'success' => false,
                'message' => 'Product is not configurable',
            ], 422);
        }

        $this->configurableProductService->deleteVariants($product);

        return response()->json([
            'success' => true,
            'message' => 'All variants deleted successfully',
        ]);
    }

    /**
     * Get variant by attributes (for AJAX requests)
     */
    public function getVariantByAttributes(Request $request, Product $product): JsonResponse
    {
        if (! $product->isConfigurable()) {
            return response()->json([
                'success' => false,
                'message' => 'Product is not configurable',
            ], 422);
        }

        $attributes = $request->input('attributes', []);
        $variant = $this->configurableProductService->findVariantByAttributes($product, $attributes);

        if (! $variant) {
            return response()->json([
                'success' => false,
                'message' => 'Variant not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'variant' => [
                'id' => $variant->id,
                'name' => $variant->variant_name,
                'sku' => $variant->sku,
                'price' => $variant->price,
                'stock' => $variant->stock,
                'image' => $variant->image_url,
            ],
        ]);
    }

    /**
     * Handle configurable product creation
     */
    private function handleConfigurableProduct(Product $product, Request $request): void
    {
        // Save configurable attributes
        $configurableAttributes = $request->input('configurable_attributes', []);
        $product->update([
            'configurable_attributes' => $configurableAttributes,
        ]);

        // Generate variants if requested
        if ($request->input('generate_variants')) {
            $this->configurableProductService->generateVariants($product, $configurableAttributes);
        }
    }

    /**
     * Handle configurable product update
     */
    private function handleConfigurableProductUpdate(Product $product, Request $request): void
    {
        $configurableAttributes = $request->input('configurable_attributes', []);

        // Check if attributes changed
        $oldAttributes = $product->configurable_attributes ?? [];
        sort($oldAttributes);
        sort($configurableAttributes);

        $attributesChanged = $oldAttributes !== $configurableAttributes;

        $product->update([
            'configurable_attributes' => $configurableAttributes,
        ]);

        // Regenerate variants if attributes changed and requested
        if ($attributesChanged && $request->input('regenerate_variants')) {
            $this->configurableProductService->deleteVariants($product);
            $this->configurableProductService->generateVariants($product, $configurableAttributes);
        }
    }
}
