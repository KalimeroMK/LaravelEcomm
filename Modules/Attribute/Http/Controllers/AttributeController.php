<?php

namespace Modules\Attribute\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Modules\Attribute\Http\Requests\Store;
use Modules\Attribute\Http\Requests\Update;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Service\AttributeService;

class AttributeController extends Controller
{
    
    public AttributeService $attribute_service;
    
    public function __construct(AttributeService $attribute_service)
    {
        $this->attribute_service = $attribute_service;
    }
    
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('attribute::index', ['attributes' => $this->attribute_service->getAll()]);
    }
    
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('attribute::create', ['attribute' => new Attribute()]);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  Store  $request
     *
     * @return RedirectResponse
     */
    public function store(Store $request)
    {
        $this->attribute_service->store($request->validated());
        
        return redirect()->route('attributes.index');
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  Attribute  $attribute
     *
     * @return Renderable
     */
    public function edit(Attribute $attribute)
    {
        $attribute = $this->attribute_service->edit($attribute->id);
        
        return view('attribute::edit', compact('attribute'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  Update  $request
     * @param  Attribute  $attribute
     *
     * @return RedirectResponse
     */
    public function update(Attribute $attribute, Update $request)
    {
        $this->attribute_service->update($attribute->id, $request->validated());
        
        return redirect()->route('attributes.index');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  Attribute  $attribute
     *
     * @return RedirectResponse
     */
    public function destroy(Attribute $attribute)
    {
        $this->attribute_service->destroy($attribute->id);
        
        return redirect()->route('attributes.index');
    }
}
