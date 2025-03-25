<?php

declare(strict_types=1);

namespace Modules\Product\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Admin\Models\Condition;
use Modules\Attribute\Models\Attribute;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Core\Http\Controllers\CoreController;
use Modules\OpenAI\Service\OpenAIService;
use Modules\Product\Http\Requests\Store;
use Modules\Product\Http\Requests\Update;
use Modules\Product\Models\Product;
use Modules\Product\Service\ProductService;
use Modules\Size\Models\Size;
use Modules\Tag\Models\Tag;

class ProductController extends CoreController
{
    private ProductService $product_service;

    public function __construct(ProductService $product_service)
    {
        $this->product_service = $product_service;
        $this->authorizeResource(Product::class, 'product');
    }

    public function index(): Renderable
    {
        return view('product::index', ['products' => $this->product_service->index()]);
    }

    public function create(): Renderable
    {
        return view('product::create', [
            'brands' => Brand::get(),
            'categories' => Category::get(),
            'product' => new Product,
            'sizes' => Size::get(),
            'conditions' => Condition::get(),
            'tags' => Tag::get(),
            'attributes' => Attribute::all(),
        ]
        );
    }

    /**
     * @throws Exception
     */
    public function store(Store $request): RedirectResponse
    {
        $this->product_service->store($request->all());

        return redirect()->route('products.index');
    }

    public function edit(Product $product): Renderable
    {
        $product->load('attributes.attribute');

        return view('product::edit', [
            'brands' => Brand::get(),
            'categories' => Category::all(),
            'product' => $product,
            'sizes' => Size::get(),
            'conditions' => Condition::all(),
            'tags' => Tag::get(),
            'attributes' => Attribute::all(),
        ]);
    }

    /**
     * @throws Exception
     */
    public function update(Update $request, Product $product): RedirectResponse
    {
        $this->product_service->update($product->id, $request->all());

        return redirect()->route('products.index');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->product_service->delete($product->id);

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
