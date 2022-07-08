<?php

namespace Modules\Category\Service;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Modules\Category\Http\Controllers\CategoryController;
use Modules\Category\Http\Requests\Store;
use Modules\Category\Http\Requests\Update;
use Modules\Category\Models\Category;

class CategoryService
{
    private CategoryController $category_controller;
    
    public function __construct(CategoryController $category_controller)
    {
        $this->category_controller = $category_controller;
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
        $title     = $request['title'];
        $parent_id = $request['parent_id'];
        $category  = $this->category_controller->get_category()->storeCategory($title, $parent_id);
        Session::flash('flash_message', 'Category successfully created!');
        
        return redirect()->route('categories.edit', $category);
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
        
        return view('category::edit', compact('category', 'categories'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        $categories = Category::getTree();
        $category   = new Category();
        
        return view('category::create', compact('categories', 'category'));
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
        $title     = $request['title'];
        $parent_id = $request['parent_id'];
        $this->category_controller->get_category()->updateCategory($title, $parent_id, $category->id);
        Session::flash('flash_message', 'Category successfully created!');
        
        return redirect()->back();
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
        $this->category_controller->get_category()->deleteCategory($category->id);
        Session::flash('flash_message', 'Category successfully deleted!');
        
        return redirect()->route('categories.index');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|RedirectResponse
     */
    public function index(): View|Factory|RedirectResponse|Application
    {
        $categories = $this->category_controller->get_category()->getAll();
        if (empty($categories)) {
            return redirect()->route('categories.create');
        }
        
        return view('category::index', compact('categories'));
    }
}