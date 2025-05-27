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
use Modules\OpenAI\Service\OpenAIService;
use Modules\Product\Actions\DeleteProductAction;
use Modules\Product\Actions\GetAllProductsAction;
use Modules\Product\Actions\StoreProductAction;
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

        return view('product::index', ['products' => $productsDto->products]);
    }

    public function create(): Renderable
    {
        return view('product::create', [
            'brands' => Brand::get()->toArray(),
            'categories' => Category::get()->toArray(),
            'product' => [],
            'tags' => Tag::get()->toArray(),
            'attributes' => Attribute::all()->toArray(),
        ]);
    }

    /**
     * @throws Exception
     */
    public function store(Store $request): RedirectResponse
    {
        $dto = ProductDTO::fromRequest($request);
        $this->storeProductAction->execute($dto);

        return redirect()->route('products.index');
    }

    public function edit(Product $product): Renderable
    {
        $product->load('attributes.attribute');
        $productDto = ProductDTO::fromModel($product);

        return view('product::edit', [
            'brands' => Brand::get()->toArray(),
            'categories' => Category::get()->toArray(),
            'product' => (array) $productDto,
            'tags' => Tag::get()->toArray(),
            'attributes' => Attribute::all()->toArray(),
        ]);
    }

    /**
     * @throws Exception
     */
    public function update(Update $request, Product $product): RedirectResponse
    {
        $dto = ProductDTO::fromRequest($request, $product->id);
        $this->updateProductAction->execute($product->id, $dto);

        return redirect()->route('products.index');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->deleteProductAction->execute($product->id);

        return redirect()->route('products.index');
    }

    public function deleteMedia(int $modelId, int $mediaId): RedirectResponse
    {
        $model = Product::findOrFail($modelId);
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
