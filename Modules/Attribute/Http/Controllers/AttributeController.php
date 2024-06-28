<?php

namespace Modules\Attribute\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Modules\Attribute\Http\Requests\Store;
use Modules\Attribute\Http\Requests\Update;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Service\AttributeService;
use Modules\Core\Http\Controllers\CoreController;

class AttributeController extends CoreController
{

    protected AttributeService $attribute_service;

    public function __construct(AttributeService $attribute_service)
    {
        $this->attribute_service = $attribute_service;
        $this->authorizeResource(Attribute::class, 'attribute');
    }

    public function index(): Renderable
    {
        return view('attribute::index', ['attributes' => $this->attribute_service->getAll()]);
    }

    public function create(): Renderable
    {
        return view('attribute::create', ['attribute' => new Attribute()]);
    }

    public function store(Store $request): RedirectResponse
    {
        $this->attribute_service->create($request->validated());

        return redirect()->route('attributes.index');
    }

    public function edit(Attribute $attribute): Renderable
    {
        return view('attribute::edit', ['attribute' => $this->attribute_service->findById($attribute->id)]);
    }

    public function update(Update $request, Attribute $attribute): RedirectResponse
    {
        $this->attribute_service->update($attribute->id, $request->validated());

        return redirect()->route('attributes.index');
    }

    public function destroy(Attribute $attribute): RedirectResponse
    {
        $this->attribute_service->delete($attribute->id);

        return redirect()->route('attributes.index');
    }
}
