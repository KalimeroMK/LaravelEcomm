<?php

namespace Modules\Category\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Category\Http\Requests\Store;
use Modules\Category\Http\Requests\Update;
use Modules\Category\Models\Category;
use Modules\Category\Repository\CategoryRepository;
use Modules\Category\Service\CategoryService;

class CategoryController extends Controller
{
    private CategoryRepository $category;
    private CategoryService $category_service;
    
    public function __construct(CategoryRepository $category)
    {
        $this->category = $category;
        $this->middleware('permission:categories-list');
        $this->middleware('permission:categories-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:categories-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:categories-delete', ['only' => ['destroy']]);
        $this->category_service = new CategoryService($this);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|RedirectResponse
     */
    public function index(): View|Factory|RedirectResponse|Application
    {
        return $this->category_service->index();
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        return $this->category_service->create();
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  Store  $request
     *
     * @return RedirectResponse
     */
    public function store(Store $request): RedirectResponse
    {
        return $this->category_service->store($request);
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  Category  $category
     *
     * @return Application|Factory|View
     */
    public function edit(Category $category): View|Factory|Application
    {
        return $this->category_service->edit($category);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  Update  $request
     * @param  Category  $category
     *
     * @return RedirectResponse
     */
    public function update(Update $request, Category $category): RedirectResponse
    {
        return $this->category_service->update($request, $category);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  Category  $category
     *
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy(Category $category): RedirectResponse
    {
        return $this->category_service->destroy($category);
    }
    
    /**
     * @return CategoryRepository
     */
    public function get_category(): CategoryRepository
    {
        return $this->category;
    }
}
