<?php

declare(strict_types=1);

namespace Modules\Attribute\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Modules\Attribute\Http\Requests\AttributeGroupRequest;
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

    public function store(AttributeGroupRequest $request): RedirectResponse
    {
        $group = AttributeGroup::create($request->validated());
        // Assign attributes to this group
        if ($request->has('attributes')) {
            \Modules\Attribute\Models\Attribute::whereIn('id', $request->attributes)
                ->update(['attribute_group_id' => $group->id]);
        }

        return redirect()->route('attribute-groups.index');
    }

    public function edit(AttributeGroup $attribute_group): Renderable
    {
        return view('attribute::groups.edit', ['group' => $attribute_group]);
    }

    public function update(AttributeGroupRequest $request, AttributeGroup $attribute_group): RedirectResponse
    {
        $attribute_group->update($request->validated());
        // Unassign all attributes from this group first
        \Modules\Attribute\Models\Attribute::where('attribute_group_id', $attribute_group->id)
            ->update(['attribute_group_id' => null]);
        // Assign selected attributes
        if ($request->has('attributes')) {
            \Modules\Attribute\Models\Attribute::whereIn('id', $request->attributes)
                ->update(['attribute_group_id' => $attribute_group->id]);
        }

        return redirect()->route('attribute-groups.index');
    }

    public function destroy(AttributeGroup $attribute_group): RedirectResponse
    {
        $attribute_group->delete();

        return redirect()->route('attribute-groups.index');
    }
}
