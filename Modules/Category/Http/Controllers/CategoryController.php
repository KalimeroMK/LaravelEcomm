<?php

namespace Modules\Category\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Modules\Category\Http\Requests\Store;
use Modules\Category\Http\Requests\Update;
use Modules\Category\Models\Category;
use Modules\Category\Service\CategoryService;

class CategoryController extends Controller
{
    private CategoryService $category_service;
    
    public function __construct(CategoryService $category_service)
    {
        $this->middleware('permission:categories-list');
        $this->middleware('permission:categories-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:categories-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:categories-delete', ['only' => ['destroy']]);
        $this->category_service = $category_service;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|RedirectResponse
     */
    public function index(): View|Factory|RedirectResponse|Application
    {
        $categories = $this->category_service->index();
        
        if (empty($categories)) {
            return redirect()->route('categories.create');
        }
        
        return view('category::index', compact('categories'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        return view('category::create', ['categories' => Category::getTree(), 'category' => new Category()]);
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
        $this->category_service->store($request->validated());
        
        return redirect()->route('categories.index');
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
        $categories = Category::getTree();
        $category   = $this->category_service->edit($category->id);
        
        return view('category::edit', compact('category', 'categories'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  Update  $request
     *
     * @return RedirectResponse
     */
    public function update(Update $request): RedirectResponse
    {
        return $this->category_service->update($request->all());
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
        $this->category_service->destroy($category->id);
        Session::flash('flash_message', 'Category successfully deleted!');
        
        return redirect()->route('categories.index');
    }
    
}
