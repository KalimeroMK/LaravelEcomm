<?php

declare(strict_types=1);

namespace Modules\Attribute\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Modules\Attribute\Actions\CreateAttributeAction;
use Modules\Attribute\Actions\DeleteAttributeAction;
use Modules\Attribute\Actions\FindAttributeAction;
use Modules\Attribute\Actions\GetAllAttributesAction;
use Modules\Attribute\Actions\UpdateAttributeAction;
use Modules\Attribute\DTOs\AttributeDTO;
use Modules\Attribute\Http\Requests\Attribute\Store;
use Modules\Attribute\Http\Requests\Attribute\Update;
use Modules\Attribute\Models\Attribute;
use Modules\Core\Http\Controllers\CoreController;

class AttributeController extends CoreController
{
    public function __construct(
        private readonly GetAllAttributesAction $getAllAttributesAction,
        private readonly FindAttributeAction $findAttributeAction,
        private readonly CreateAttributeAction $createAction,
        private readonly UpdateAttributeAction $updateAction,
        private readonly DeleteAttributeAction $deleteAction
    ) {
        $this->authorizeResource(Attribute::class, 'attribute');
    }

    public function index(): Renderable
    {
        $this->authorize('viewAny', Attribute::class);

        return view('attribute::index', [
            'attributes' => $this->getAllAttributesAction->execute(),
        ]);
    }

    public function create(): Renderable
    {
        $this->authorize('create', Attribute::class);

        return view('attribute::create', [
            'attribute' => new Attribute,
        ]);
    }

    public function store(Store $request): RedirectResponse
    {
        $this->createAction->execute(AttributeDTO::fromRequest($request));

        return redirect()->route('attributes.index')
            ->with('success', __('Attribute created successfully.'));
    }

    public function edit(Attribute $attribute): Renderable
    {
        return view('attribute::edit', [
            'attribute' => $this->findAttributeAction->execute($attribute->id),
        ]);
    }

    public function update(Update $request, Attribute $attribute): RedirectResponse
    {
        $this->updateAction->execute(
            AttributeDTO::fromRequest($request, $attribute->id, $attribute)
        );

        return redirect()->route('attributes.index')
            ->with('success', __('Attribute updated successfully.'));
    }

    public function destroy(Attribute $attribute): RedirectResponse
    {
        $this->deleteAction->execute($attribute->id);

        return redirect()->route('attributes.index')
            ->with('success', __('Attribute deleted successfully.'));
    }
}
