<?php

declare(strict_types=1);

namespace Modules\Attribute\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Attribute\Actions\AttributeGroup\CreateAttributeGroupAction;
use Modules\Attribute\Actions\AttributeGroup\DeleteAttributeGroupAction;
use Modules\Attribute\Actions\AttributeGroup\UpdateAttributeGroupAction;
use Modules\Attribute\DTOs\AttributeGroupDTO;
use Modules\Attribute\Http\Requests\AttributeGroup\Store;
use Modules\Attribute\Http\Requests\AttributeGroup\Update;
use Modules\Attribute\Models\AttributeGroup;
use Modules\Attribute\Repository\AttributeGroupRepository;
use Modules\Core\Http\Controllers\CoreController;

class AttributeGroupController extends CoreController
{
    public function __construct(
        private readonly AttributeGroupRepository $repository,
        private readonly CreateAttributeGroupAction $createAction,
        private readonly UpdateAttributeGroupAction $updateAction,
        private readonly DeleteAttributeGroupAction $deleteAction
    ) {
        $this->authorizeResource(AttributeGroup::class, 'attribute_group');
    }

    public function index(): View
    {
        return view('attribute::groups.index', [
            'groups' => $this->repository->findAll(),
        ]);
    }

    public function create(): View
    {
        return view('attribute::groups.create', [
            'group' => new AttributeGroup,
        ]);
    }

    public function store(Store $request): RedirectResponse
    {
        $dto = AttributeGroupDTO::fromRequest($request);
        $this->createAction->execute($dto);

        return redirect()->route('attribute-groups.index')
            ->with('success', __('Attribute group created successfully.'));
    }

    public function edit(AttributeGroup $attribute_group): View
    {
        return view('attribute::groups.edit', [
            'group' => $this->repository->findById($attribute_group->id),
        ]);
    }

    public function update(Update $request, AttributeGroup $attribute_group): RedirectResponse
    {
        $dto = AttributeGroupDTO::fromRequest($request)->withId($attribute_group->id);
        $group = $this->updateAction->execute($dto);

        $group->attributes()->sync($request->attributes ?? []);

        return redirect()->route('attribute-groups.index')
            ->with('success', __('Attribute group updated successfully.'));
    }

    public function destroy(AttributeGroup $attribute_group): RedirectResponse
    {
        $this->deleteAction->execute($attribute_group->id);

        return redirect()->route('attribute-groups.index')
            ->with('success', __('Attribute group deleted successfully.'));
    }
}
