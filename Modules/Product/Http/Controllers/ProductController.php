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
use Modules\Product\Actions\DeleteProductAction;
use Modules\Product\Actions\GetAllProductsAction;
use Modules\Product\Actions\StoreProductAction;
use Modules\Product\Actions\UpdateProductAction;
use Modules\Product\DTOs\ProductDTO;
use Modules\Product\Models\Product;
use Modules\Tag\Models\Tag;

class ProductController extends CoreController
{
    public function __construct()
    {
        $this->authorizeResource(Product::class, 'product');
    }

    public function index(): Renderable
    {
        $productsDto = (new GetAllProductsAction())->execute();

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
    public function store(Request $request): RedirectResponse
    {
        (new StoreProductAction())->execute($request->all());

        return redirect()->route('products.index');
    }

    public function edit(Product $product): Renderable
    {
        $product->load('attributes.attribute');
        $productDto = new ProductDTO($product);

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
    public function update(Request $request, Product $product): RedirectResponse
    {
        (new UpdateProductAction())->execute($product->id, $request->all());

        return redirect()->route('products.index');
    }

    public function destroy(Product $product): RedirectResponse
    {
        (new DeleteProductAction())->execute($product->id);

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
