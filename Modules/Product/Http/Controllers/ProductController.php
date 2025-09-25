<?php

declare(strict_types=1);

namespace Modules\Product\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Attribute\Models\Attribute;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Core\Support\Media\MediaUploader;
use Modules\Core\Support\Relations\SyncRelations;
use Modules\OpenAI\Service\OpenAIService;
use Modules\Product\Actions\DeleteProductAction;
use Modules\Product\Actions\GetAllProductsAction;
use Modules\Product\Actions\StoreProductAction;
use Modules\Product\Actions\SyncProductAttributesAction;
use Modules\Product\Actions\UpdateProductAction;
use Modules\Product\DTOs\ProductDTO;
use Modules\Product\Http\Requests\Store;
use Modules\Product\Http\Requests\Update;
use Modules\Product\Models\Product;
use Modules\Tag\Models\Tag;

class ProductController extends CoreController
{
    private GetAllProductsAction $getAllProductsAction;

    private StoreProductAction $storeProductAction;

    private UpdateProductAction $updateProductAction;

    private DeleteProductAction $deleteProductAction;

    public function __construct(
        GetAllProductsAction $getAllProductsAction,
        StoreProductAction $storeProductAction,
        UpdateProductAction $updateProductAction,
        DeleteProductAction $deleteProductAction
    ) {
        $this->getAllProductsAction = $getAllProductsAction;
        $this->storeProductAction = $storeProductAction;
        $this->updateProductAction = $updateProductAction;
        $this->deleteProductAction = $deleteProductAction;
        $this->authorizeResource(Product::class, 'product');
    }

    public function index(): Renderable
    {
        $productsDto = $this->getAllProductsAction->execute();
        $hotProducts = Product::with(['brand', 'categories', 'tags', 'attributeValues.attribute'])->where('is_featured', true)->get();

        return view('product::index', [
            'products' => $productsDto->products,
            'hot_products' => $hotProducts,
        ]);
    }

    public function create(): Renderable
    {
        $attributes = Attribute::with('options')->get();

        return view('product::create', [
            'brands' => Brand::get()->toArray(),
            'categories' => Category::get()->toArray(),
            'product' => [],
            'tags' => Tag::get()->toArray(),
            'attributes' => $attributes,
        ]);
    }

    /**
     * @throws Exception
     */
    public function store(Store $request): RedirectResponse
    {
        $dto = ProductDTO::fromRequest($request);
        $product = $this->storeProductAction->execute($dto);
        SyncRelations::execute($product, [
            'categories' => $dto->categories,
            'tags' => $dto->tags,
            'brand' => $dto->brand_id,
            'attributes' => $dto->attributes ?? [],
        ]);
        MediaUploader::uploadMultiple($product, ['images'], 'product');
        SyncProductAttributesAction::execute($product, $dto->attributes ?? []);

        return redirect()->route('products.index');
    }

    public function edit(Product $product): Renderable
    {
        $attributes = Attribute::with('options')->get();
        $brands = Brand::get();
        $categories = Category::get();
        $tags = Tag::get();
        $product->load(['attributeValues.attribute']);

        return view('product::edit', ['brands' => $brands, 'categories' => $categories, 'product' => $product, 'tags' => $tags, 'attributes' => $attributes]);
    }

    /**
     * @throws Exception
     */
    public function update(Update $request, Product $product): RedirectResponse
    {
        $dto = ProductDTO::fromRequest($request, $product->id);
        $this->updateProductAction->execute($product->id, $dto);
        SyncRelations::execute($product, [
            'categories' => $dto->categories,
            'tags' => $dto->tags,
            'brand' => $dto->brand_id,
        ]);
        // Save product attribute values
        SyncProductAttributesAction::execute($product, $dto->attributes ?? []);
        MediaUploader::uploadMultiple($product, ['images'], 'product');

        return redirect()->route('products.index');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->deleteProductAction->execute($product->id);

        return redirect()->route('products.index');
    }

    public function deleteMedia(int $modelId, int $mediaId): RedirectResponse
    {
        $model = Product::with(['brand', 'categories', 'tags', 'attributeValues.attribute'])->findOrFail($modelId);
        $model->media()->where('id', $mediaId)->first()->delete();

        return back()->with('success', 'Media deleted successfully.');
    }

    public function generateDescription(Request $request)
    {
        $title = $request->title;
        $description = (new OpenAIService)->generateProductDescription($title);

        return response()->json(['description' => $description]);
    }
}
