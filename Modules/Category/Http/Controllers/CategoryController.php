<?php

declare(strict_types=1);

namespace Modules\Category\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Category\Actions\CreateCategoryAction;
use Modules\Category\Actions\DeleteCategoryAction;
use Modules\Category\Actions\GetAllCategoriesAction;
use Modules\Category\Actions\GetCategoryTreeAction;
use Modules\Category\Actions\UpdateCategoryAction;
use Modules\Category\Actions\UpdateCategoryOrderAction;
use Modules\Category\DTOs\CategoryDTO;
use Modules\Category\Http\Requests\Store;
use Modules\Category\Http\Requests\Update;
use Modules\Category\Models\Category;
use Modules\Category\Repository\CategoryRepository;
use Modules\Core\Http\Controllers\CoreController;

class CategoryController extends CoreController
{
    public function __construct(
        private readonly CategoryRepository $repository,
        private readonly GetAllCategoriesAction $getAllCategoriesAction,
        private readonly GetCategoryTreeAction $getCategoryTreeAction,
        private readonly CreateCategoryAction $createAction,
        private readonly UpdateCategoryAction $updateAction,
        private readonly DeleteCategoryAction $deleteAction,
        private readonly UpdateCategoryOrderAction $updateOrderAction
    ) {
        $this->authorizeResource(Category::class, 'category');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|Application|RedirectResponse|View
     */
    public function index()
    {
        $categories = $this->getAllCategoriesAction->execute();
        if ($categories->isEmpty()) {
            return redirect()->route('categories.create');
        }

        return view('category::index', ['categories' => $categories]);
    }

    public function create(): View
    {
        return view('category::create', [
            'categories' => $this->getCategoryTreeAction->execute(),
            'category' => new Category,
        ]);
    }

    public function store(Store $request): RedirectResponse
    {
        $this->createAction->execute(CategoryDTO::fromRequest($request));

        return redirect()->route('categories.index');
    }

    public function edit(Category $category): View
    {
        return view('category::edit', [
            'category' => $this->repository->findById($category->id),
            'categories' => $this->getCategoryTreeAction->execute(),
        ]);
    }

    public function update(Update $request, Category $category): RedirectResponse
    {
        $this->updateAction->execute(CategoryDTO::fromRequest($request, $category->id, $category));

        return redirect()->route('categories.index');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->deleteAction->execute($category->id);

        return redirect()->route('categories.index')->with('flash_message', 'Category successfully deleted!');
    }

    /**
     * Update category order (for nested set drag-and-drop).
     */
    public function updateCategoryOrder(\Illuminate\Http\Request $request): RedirectResponse
    {
        $this->authorize('update', Category::class);

        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.parent_id' => 'nullable|exists:categories,id',
            'categories.*.order' => 'sometimes|integer',
        ]);

        $this->updateOrderAction->execute($request->input('categories'));

        return redirect()->back()->with('success', 'Category order updated successfully.');
    }
}
