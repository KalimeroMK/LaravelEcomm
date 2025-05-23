<?php

declare(strict_types=1);

namespace Modules\Attribute\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Modules\Attribute\Http\Requests\AttributeGroup\Store;
use Modules\Attribute\Http\Requests\AttributeGroup\Update;
use Modules\Attribute\Models\AttributeGroup;
use Modules\Core\Http\Controllers\CoreController;

class AttributeGroupController extends CoreController
{
    public function index(): Renderable
    {
        return view('attribute::groups.index', ['groups' => AttributeGroup::all()]);
    }

    public function create(): Renderable
    {
        return view('attribute::groups.create', ['group' => new AttributeGroup]);
    }

    public function store(Store $request): RedirectResponse
    {
        $group = AttributeGroup::create($request->validated());
        // Assign attributes to this group using pivot table
        if ($request->has('attributes')) {
            $group->attributes()->sync($request->attributes);
        }

        return redirect()->route('attribute-groups.index');
    }

    public function edit(AttributeGroup $attribute_group): Renderable
    {
        return view('attribute::groups.edit', ['group' => $attribute_group]);
    }

    public function update(Update $request, AttributeGroup $attribute_group): RedirectResponse
    {
        $attribute_group->update($request->validated());
        // Sync attributes for this group via pivot table
        $attribute_group->attributes()->sync($request->attributes ?? []);

        return redirect()->route('attribute-groups.index');
    }

    public function destroy(AttributeGroup $attribute_group): RedirectResponse
    {
        $attribute_group->delete();

        return redirect()->route('attribute-groups.index');
    }
}
