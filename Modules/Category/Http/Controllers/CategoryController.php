<?php

namespace Modules\Category\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
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

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|Application|RedirectResponse|View
     */
    public function index()
    {
        $categories = $this->category_service->getAll();

        if ($categories->isEmpty()) {
            return redirect()->route('categories.create');
        }

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
        $this->category_service->create($request->validated());

        return redirect()->route('categories.index');
    }

    public function edit(Category $category): View
    {
        return view('category::edit', [
            'category' => $this->category_service->findById($category->id),
            'categories' => Category::getTree()
        ]);
    }

    public function update(Update $request, Category $category): RedirectResponse
    {
        $this->category_service->update($category->id, $request->all());

        return redirect()->route('categories.index');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->category_service->delete($category->id);

        return redirect()->route('categories.index')->with('flash_message', 'Category successfully deleted!');
    }
}
