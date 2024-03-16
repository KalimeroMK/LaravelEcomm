<?php

namespace Modules\Category\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Category\Http\Requests\Api\Store;
use Modules\Category\Http\Requests\Api\Update;
use Modules\Category\Models\Category;
use Modules\Category\Service\CategoryService;
use Modules\Core\Http\Controllers\CoreController;

class CategoryController extends CoreController
{
    protected CategoryService $category_service;

    public function __construct(CategoryService $category_service)
    {
        $this->authorizeResource(Category::class, 'category');
        $this->category_service = $category_service;
    }

    public function index()
    {
        $categories = Category::getTree();

        return view('category::index', compact('categories'));
    }

    public function create(): View
    {
        return view('category::create', [
            'categories' => Category::getTree(),
            'category' => new Category()
        ]);
    }

    public function store(Store $request): RedirectResponse
    {
        $this->category_service->store($request->validated());

        return redirect()->route('categories.index');
    }

    public function edit(Category $category): View
    {
        return view('category::edit', [
            'category' => $this->category_service->edit($category->id),
            'categories' => Category::getCategoriesArray()
        ]);
    }

    public function update(Update $request, Category $category): RedirectResponse
    {
        $this->category_service->update($category->id, $request->all());

        return redirect()->route('categories.index');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->category_service->destroy($category->id);

        return redirect()->route('categories.index')->with('flash_message', 'Category successfully deleted!');
    }

    public function updateCategoryOrder(Request $request)
    {
        try {
            $categories = $request->input('order');

            // Recursive function to update nested set values
            $updateNestedSet = function ($categories, $parent_id = null, $left = 0) use (&$updateNestedSet) {
                foreach ($categories as $category) {
                    $categoryModel = Category::find($category['id']);
                    $categoryModel->update([
                        'parent_id' => $parent_id,
                        '_lft' => ++$left,
                        '_rgt' => ++$left + (isset($category['children']) ? count($category['children']) : 0)
                    ]);

                    if (isset($category['children']) && is_array($category['children'])) {
                        $left = $updateNestedSet($category['children'], $categoryModel->id, $left);
                    }
                }

                return $left;
            };

            $updateNestedSet($categories);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
