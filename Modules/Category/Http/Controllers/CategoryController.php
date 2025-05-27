<?php

declare(strict_types=1);

namespace Modules\Category\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Category\Actions\CreateCategoryAction;
use Modules\Category\Actions\DeleteCategoryAction;
use Modules\Category\Actions\UpdateCategoryAction;
use Modules\Category\DTOs\CategoryDTO;
use Modules\Category\Http\Requests\Store;
use Modules\Category\Http\Requests\Update;
use Modules\Category\Models\Category;
use Modules\Core\Http\Controllers\CoreController;

class CategoryController extends CoreController
{
    private CreateCategoryAction $createAction;

    private UpdateCategoryAction $updateAction;

    private DeleteCategoryAction $deleteAction;

    public function __construct(
        CreateCategoryAction $createAction,
        UpdateCategoryAction $updateAction,
        DeleteCategoryAction $deleteAction
    ) {
        $this->authorizeResource(Category::class, 'category');
        $this->createAction = $createAction;
        $this->updateAction = $updateAction;
        $this->deleteAction = $deleteAction;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|Application|RedirectResponse|View
     */
    public function index()
    {
        $categories = Category::all();
        if ($categories->isEmpty()) {
            return redirect()->route('categories.create');
        }

        return view('category::index', ['categories' => $categories]);
    }

    public function create(): View
    {
        return view('category::create', [
            'categories' => Category::getTree(),
            'category' => new Category,
        ]);
    }

    public function store(Store $request): RedirectResponse
    {
        $dto = CategoryDTO::fromRequest($request);
        $this->createAction->execute($dto);

        return redirect()->route('categories.index');
    }

    public function edit(Category $category): View
    {
        return view('category::edit', [
            'category' => Category::findOrFail($category->id),
            'categories' => Category::getTree(),
        ]);
    }

    public function update(Update $request, Category $category): RedirectResponse
    {
        $dto = CategoryDTO::fromRequest($request, $category->id);
        $this->updateAction->execute($dto);

        return redirect()->route('categories.index');
    }

    public function destroy(Category $category): RedirectResponse
    {
        return redirect()->route('categories.index')->with('flash_message', 'Category successfully deleted!');
    }
}
